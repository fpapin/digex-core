<?php

namespace Digex;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Digex\Provider\ConfigurationServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;

/**
 * @author Damien Pitard <dpitard at digitas dot fr>
 * @copyright Digitas France
 */
abstract class StandardControllerProvider implements ControllerProviderInterface, ServiceProviderInterface
{
    protected function isEnabled(Application $app, $name)
    {
        return (isset($app['app.providers'][$name]) &&  $app['app.providers'][$name]);
    }
    
    public function register(Application $app)
    {
        if (!isset($app['app_dir'])) {
            throw new \Exception('"app_dir" parameter is undefined');
        }
        
        if (!isset($app['vendor_dir'])) {
            throw new \Exception('"vendor_dir" parameter is undefined');
        }
        
        if (!isset($app['config_dir'])) {
            $app['config_dir'] = $app['app_dir'] . '/config';
        }
        
        //register Configuration extension
        $app->register(new ConfigurationServiceProvider());
        
        if ($this->isEnabled($app, 'session')) {
            $app->register(new SessionServiceProvider());
        }
        
        //register UrlGenerator extension
        if ($this->isEnabled($app, 'url_generator')) {
            $app->register(new UrlGeneratorServiceProvider());
        }

        //register Twig
        if ($this->isEnabled($app, 'twig')) {

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
        if ($this->isEnabled($app, 'monolog')) {
            $app->register(new MonologServiceProvider(), array(
                'monolog.logfile'       => $app['app_dir'].'/logs/app.log',
                'monolog.class_path'    => $app['vendor_dir'].'/monolog/src',
                'monolog.name' => 'app'
            ));
        }
    }
}
