<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ServiceProviderInterface;
use Digex\Provider\ConfigurationServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

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
            throw new \Exception('Undefined $app["app_dir"] parameter');
        }
        
        if (!isset($app['vendor_dir'])) {
            throw new \Exception('Undefined $app["vendor_dir"] parameter');
        }
        
        if (!isset($app['config_dir'])) {
            $app['config_dir'] = $app['app_dir'] . '/config';
        }
        
        //register Configuration extension
        $app->register(new ConfigurationServiceProvider());
        
        if (self::isEnabled($app, 'session')) {
            $app->register(new SessionServiceProvider());
        }
        
        //register UrlGenerator extension
        if (self::isEnabled($app, 'url_generator')) {
            $app->register(new UrlGeneratorServiceProvider());
        }

        //register Twig
        if (self::isEnabled($app, 'twig')) {

            if (!isset($app['twig.path'])) {
                $app['twig.path'] = $app['app_dir'] . '/Resources/views';
            }
            
            if (!isset($app['twig.class_path'])) {
                $app['twig.class_path'] = $app['vendor_dir'] . '/twig/lib';
            }
            
            if (!isset($app['twig.options']) && (!isset($app['debug']) || !$app['debug'])) {
                $app['twig.options'] = array('cache' => $app['app_dir'] . '/cache/twig');
            }
            
            $app->register(new TwigServiceProvider());
        }

        //register Monolog
        if (self::isEnabled($app, 'monolog')) {
            $app->register(new MonologServiceProvider(), array(
                'monolog.logfile'       => $app['app_dir'].'/logs/app.log',
                'monolog.class_path'    => $app['vendor_dir'].'/monolog/src',
                'monolog.name' => 'app'
            ));
        }
        
        //register Doctrine DBAL
        if (self::isEnabled($app, 'doctrine-dbal')) {

            if (!isset($app['twig.driver'])) {
                $app['db.driver'] = 'pdo_mysql';
            }
            
            $app->register(new DoctrineServiceProvider(), array(
                'db.options'    => array(
                    'driver'    => $app['db.driver'],
                    'dbname'    => isset($app['db.dbname'])?$app['db.dbname']:null,
                    'host'      => $app['db.host']?$app['db.host']:null,
                    'user'      => $app['db.user']?$app['db.user']:null,
                    'password'  => $app['db.password']?$app['db.password']:null,
                ),
                'db.dbal.class_path'    => $app['vendor_dir'].'/doctrine-dbal/lib',
                'db.common.class_path'  => $app['vendor_dir'].'/doctrine-common/lib'
            ));
        }
    }
}
