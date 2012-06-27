<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ServiceProviderInterface;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\SymfonyBridgesServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Digex\Provider\ConfigurationServiceProvider;
use Digex\Console\Command\VendorInitCommand;

/**
 * @author Damien Pitard <dpitard at digitas dot fr>
 * @copyright Digitas France
 */
class LazyRegisterServiceProvider implements ServiceProviderInterface
{
    /**
     * Is the provider set in configuration
     *
     * @param Application $app
     * @param string $name
     * @return boolean
     */
    static public function isEnabled(Application $app, $name)
    {
        return (isset($app['app.providers'][$name]) &&  $app['app.providers'][$name]);
    }

    public function register(Application $app)
    {
        if (!isset($app['app_dir'])) {
            throw new \Exception('Undefined "app_dir" parameter');
        }

        if (!isset($app['vendor_dir'])) {
            throw new \Exception('Undefined "vendor_dir" parameter');
        }

        if (!isset($app['config_dir'])) {
            $app['config_dir'] = $app['app_dir'] . '/config';
        }

        //register Configuration extension
        $app->register(new ConfigurationServiceProvider());

        if (self::isEnabled($app, 'session')) {

            if (isset($app['session.cookie_lifetime'])) {

                $options = array(
                    'cookie_lifetime' => $app['session.cookie_lifetime']
                );

                if (isset($app['session.storage.options'])) {
                    $app['session.storage.options'] = array_merge($app['session.storage.options'], $options);
                } else {
                    $app['session.storage.options'] = $options;
                }
            }

            $app->register(new SessionServiceProvider());
        }

        //register UrlGenerator extension
        if (self::isEnabled($app, 'url_generator')) {
            $app->register(new UrlGeneratorServiceProvider());
        }

        //register Twig
        if (self::isEnabled($app, 'twig')) {

            $app->register(new TwigServiceProvider(), array(
                'twig.path'=> $app['app_dir'] . '/Resources/views',
                'twig.options' => array('cache' => $app['app_dir'] . '/cache/twig')
                )
            );
        }

        //register Monolog
        if (self::isEnabled($app, 'monolog')) {
            $app->register(new MonologServiceProvider(), array(
                'monolog.logfile'       => $app['app_dir'].'/logs/app.log',
                'monolog.name' => 'app'
            ));
        }

        //register Doctrine DBAL
        if (self::isEnabled($app, 'doctrine')) {

            if (!isset($app['db.driver'])) {
                $app['db.driver'] = 'pdo_mysql';
            }

            $app->register(new DoctrineServiceProvider(), array(
                'db.options'    => array(
                    'driver'    => $app['db.driver'],
                    'dbname'    => isset($app['db.dbname'])?$app['db.dbname']:null,
                    'host'      => $app['db.host']?$app['db.host']:null,
                    'user'      => $app['db.user']?$app['db.user']:null,
                    'password'  => $app['db.password']?$app['db.password']:null,
                )
            ));

            if (!isset($app['db.entities'])) {
                $app['db.entities'] = array();
            }

            $app->register(new DoctrineORMServiceProvider(), array(
                'db.proxy_dir' => $app['app_dir'] . '/cache/proxies',
                'db.proxy_namespace' => 'DoctrineORMProxy',
                'db.entities' => $app['db.entities']
            ));
        }

        //register TranslationServiceProvider
        if (self::isEnabled($app, 'translation')) {

            $app->register(new TranslationServiceProvider(), array(
				'locale_fallback' => $app['translation.locale_fallback']
			));

			$app['translator.loader'] = $app->share(function () {
				return new YamlFileLoader();
			});

			//$app['translator.domains'] = array();
			$domains = array();
            foreach($app['translation.locales'] as $locale => $filename) {
                $domains['messages'][$locale] = $app['app_dir'] . '/locales/' . $filename;
            }
			$app['translator.domains'] = $domains;

            $app->before(function () use ($app) {
                if ($locale = $app['request']->get('locale')) {
                    $app['locale'] = $locale;
                }
            });
        }
    }

	public function boot(Application $app)
	{
	}
}
