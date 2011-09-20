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
 */
class ConfigurationExtension implements ExtensionInterface
{
    public function register(Application $app)
    {
        if (!isset($app['config_dir'])) {
            throw new \Exception("Please set a \$app['config_dir'] parameter");
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
        
        return $app;
    }
}