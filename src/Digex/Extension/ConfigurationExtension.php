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
        
        //
        foreach($parameters as $groupName => $group) {
            foreach($group as $name => $parameter) {
                $app[$groupName . '.' . $name] = $parameter;
            }
        }

        //enable/disable the debug mode
        if (isset($parameters['app']['debug'])) {
            $app['debug'] = $parameters['app']['debug'];
        }
        
        //set the charset
        if (isset($parameters['app']['charset'])) {
            $app['charset'] = $parameters['app']['charset'];
        }
        
        return $app;
    }
}