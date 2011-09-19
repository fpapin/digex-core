<?php

namespace Digex\Extension;

use Silex\Application;
use Silex\ExtensionInterface;
use Digex\YamlConfigLoader;

/**
 * This is a lazzy extension that enables some common extension for a standard
 * Digitas application, init some parameters and load config files.
 * 
 * @author Damien Pitard <dpitard at digitas.fr>
 * @version $Id$
 */
class ApplicationExtension implements ExtensionInterface {

    public function register(Application $app)
    {
        if (!isset($app['root_dir'])) {
            throw new \Exception("Please set a \$app['root_dir'] parameter");
        }
                
        if (!isset($app['config_dir'])) {
            $app['config_dir'] = $app['root_dir'] . '/config';
        }
        
        //load the config
        $loader = new YamlConfigLoader();
        $parameters = $loader->load($app['config_dir']);
        
        //enable/disable the debug mode
        if (isset($parameters['app']['debug'])) {
            $app['debug'] = $parameters['app']['debug'];
        }
        
        //inject config into the container
        $app['config'] = $parameters;
        
        //register UrlGeneratorExtension
        if (isset($parameters['app']['extensions']['url_generator']) && $parameters['app']['extensions']['url_generator']) {
            $app->register(new \Silex\Extension\UrlGeneratorExtension());
        }

        //register Twig
        if (isset($parameters['app']['extensions']['twig']) && $parameters['app']['extensions']['twig']) {

            if (!isset($app['twig.path'])) {
                $app['twig.path'] = $app['root_dir'] . '/views';
            }
            
            if (!isset($app['twig.class_path'])) {
                $app['twig.class_path'] = $app['root_dir'] . '/vendor/twig/lib';
            }
            
            if (!isset($app['twig.options']) && (!isset($app['debug']) || !$app['debug'])) {
                $app['twig.options'] = array('cache' => $app['root_dir'] . '/cache/twig');
            }
            
            $app->register(new \Silex\Extension\TwigExtension());
        }

        //register Monolog
        if (isset($parameters['app']['extensions']['monolog']) && $parameters['app']['extensions']['monolog']) {
            $app->register(new \Silex\Extension\MonologExtension(), array(
                'monolog.logfile'       => $app['root_dir'].'/logs/app.log',
                'monolog.class_path'    => $app['root_dir'].'/vendor/monolog/src',
                'monolog.name' => 'app'
            ));
        }
    }
}