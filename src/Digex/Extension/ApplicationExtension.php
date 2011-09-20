<?php

namespace Digex\Extension;

use Silex\Application;
use Silex\ExtensionInterface;

/**
 * This is a lazzy extension that enables some common extension for a standard
 * Digitas application, init some parameters and load config files.
 * 
 * @author Damien Pitard <dpitard at digitas.fr>
 */
class ApplicationExtension implements ExtensionInterface
{

    public function register(Application $app)
    {
        if (!isset($app['app_dir'])) {
            throw new \Exception("Please set a \$app['app_dir'] parameter");
        }
        
        if (!isset($app['vendor_dir'])) {
            throw new \Exception("Please set a \$app['vendor_dir'] parameter");
        }
                
        if (!isset($app['config_dir'])) {
            $app['config_dir'] = $app['app_dir'] . '/config';
        }

        $app->register(new ConfigurationExtension());
        
        //register UrlGeneratorExtension
        if (isset( $app['config']['app']['extensions']['url_generator']) &&  $app['config']['app']['extensions']['url_generator']) {
            $app->register(new \Silex\Extension\UrlGeneratorExtension());
        }

        //register Twig
        if (isset( $app['config']['app']['extensions']['twig']) &&  $app['config']['app']['extensions']['twig']) {

            if (!isset($app['twig.path'])) {
                $app['twig.path'] = $app['app_dir'] . '/Resources/views';
            }
            
            if (!isset($app['twig.class_path'])) {
                $app['twig.class_path'] = $app['vendor_dir'] . '/twig/lib';
            }
            
            if (!isset($app['twig.options']) && (!isset($app['debug']) || !$app['debug'])) {
                $app['twig.options'] = array('cache' => $app['app_dir'] . '/cache/twig');
            }
            
            $app->register(new \Silex\Extension\TwigExtension());
        }

        //register Monolog
        if (isset( $app['config']['app']['extensions']['monolog']) &&  $app['config']['app']['extensions']['monolog']) {
            $app->register(new \Silex\Extension\MonologExtension(), array(
                'monolog.logfile'       => $app['app_dir'].'/logs/app.log',
                'monolog.class_path'    => $app['vendor_dir'].'/monolog/src',
                'monolog.name' => 'app'
            ));
        }
    }
}