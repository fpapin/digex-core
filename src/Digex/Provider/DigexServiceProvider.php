<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Digex\Console\Console;

/**
 * DigexServiceProvider.
 *
 * @author    Damien Pitard <damien.pitard@digitas.fr>
 * @copyright 2012 Digitas France
 */
class DigexServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['digex.init'] = $app->protect(function($app) {
            if (!isset($app['digex.app_dir'])) {
                if (PHP_SAPI === 'cli') {
                    $app['digex.app_dir'] = getcwd() . '/app';
                } else {
                    $app['digex.app_dir'] = getcwd() . '/../app';
                }
            }

            $env = getenv('APP_ENV') ?: 'dev';

            if (PHP_SAPI === 'cli') {
                $app->register(new \Digex\Provider\ConsoleServiceProvider());

                $input = new \Symfony\Component\Console\Input\ArgvInput();
                $env = $input->getParameterOption(array('--env', '-e'), $env);

                if ($input->hasParameterOption(array('--no-debug', ''))) {
                    $app['debug'] = false;
                }
            }
            $app['env'] = $env;

            if (class_exists('Symfony\Component\Translation\Translator')) {

                $app->register(new \Silex\Provider\TranslationServiceProvider());

                if (isset($app['locales_allowed'])) {
                    $app->before(function () use ($app) {
                        if (!in_array($app['locale'], $app['locales_allowed'])) {
                            throw new \Exception(sprintf('Locale "%s" is not allowed. see "translattion.allowed" in app/config.yml.', $app['locale']));
                        }
                    });
                }
            }

            if (class_exists('Igorw\Silex\ConfigServiceProvider')) {

                if (!isset($app['digex.config_files'])) {
                    $app['digex.config_files'] = array(
                        $app['digex.app_dir'] . '/config/config.yml',
                        $app['digex.app_dir'] . "/config/config_{$app['env']}.yml",
                    );
                }

                foreach ($app['digex.config_files'] as $filename) {
                    if (file_exists($filename)) {
                        $app->register(new \Igorw\Silex\ConfigServiceProvider($filename));
                    }
                }
            }

            if (PHP_SAPI === 'cli') {
                if ($input->hasParameterOption(array('--no-debug', ''))) {
                    $app['debug'] = false;
                }
            }

            if (class_exists('Monolog\Logger')) {

                if (!isset($app['digex.logs_dir'])) {
                    $app['digex.logs_dir'] = $app['digex.app_dir'] . '/logs';
                }

                $app->register(new \Silex\Provider\MonologServiceProvider(), array(
                    'monolog.logfile' => $app['digex.logs_dir'] . '/' . (isset($env) ? $env : 'prod') . '.log',
                    'monolog.name' => 'digex'
                ));
            }

            if (class_exists('Doctrine\DBAL\Connection')) {

                $app->register(new \Silex\Provider\DoctrineServiceProvider());
            }

            if (class_exists('Doctrine\ORM\EntityManager')) {

                if (!isset($app['digex.cache_dir'])) {
                    $app['digex.cache_dir'] = $app['digex.app_dir'] . '/cache';
                }

                $options = array(
                    'proxy_dir'         => $app['digex.cache_dir'] . '/' . (isset($env) ? $env : 'prod') . '/proxies',
                    'proxy_namespace'   => 'DoctrineORMProxy'
                );

                if (isset($app['em.options'])) {
                    $options = array_merge($options, $app['em.options']);
                }

                $app->register(new \Digex\Provider\DoctrineORMServiceProvider(), array(
                    'em.options' => $options
                ));

                //registrer doctrine:fixtures:load command
                if (class_exists('\Doctrine\Common\DataFixtures\AbstractFixture') && isset($app['console'])) {

                    $app['console']->add(new \Digex\Console\Command\LoadDataFixturesDoctrineCommand());
                }
            }

            //annotation reader
            if (class_exists('Doctrine\Common\Annotations\AnnotationRegistry')) {
                $loader = new \Composer\Autoload\ClassLoader();

                if (!isset($app['digex.loader_file'])) {
                    $app['digex.loader_file'] = $app['digex.app_dir'] . '/../vendor/autoload.php';
                }

                $loader = require $app['digex.loader_file'];
                \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

                $app->register(new \Digex\Provider\AnnotationReaderServiceProvider());
            }

            if (class_exists('Symfony\Component\Validator\Validator')) {
                $app->register(new \Silex\Provider\ValidatorServiceProvider());
                $app->register(new \Digex\Provider\AnnotationValidatorServiceProvider());
            }


            $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

            if (class_exists('Twig_Environment')) {

                if (!isset($app['digex.template_dir'])) {
                    $app['digex.template_dir'] = $app['digex.app_dir'] . '/views';
                }

                if (!isset($app['digex.cache_dir'])) {
                    $app['digex.cache_dir'] = $app['digex.app_dir'] . '/cache';
                }



                $app->register(new \Silex\Provider\TwigServiceProvider(), array(
                    'twig.path' => $app['digex.template_dir'],
                    'twig.options' => array(
                        'cache' => $app['digex.cache_dir'] . '/'  . (isset($env) ? $env : 'prod') . '/twig',
                        'debug' => $app['debug']
                    )
                ));

                $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
                    $twig->addGlobal('app', $app);
                    $twig->addGlobal('_locale', $app['request']->getLocale());

                    return $twig;
                }));

                $app->register(new \Digex\Provider\AssetServiceProvider());
            }

            if (class_exists('Symfony\Component\Form\Form')) {
                $app->register(new \Silex\Provider\FormServiceProvider());
            }

            if (class_exists('Symfony\Component\Security\Core\SecurityContext')) {
                $app->register(new \Silex\Provider\SessionServiceProvider());
                $app->register(new \Silex\Provider\SecurityServiceProvider());
            }
        });

        $app['digex.init']($app);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}