<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Digex\YamlConfigLoader;

/**
 * This is a lazzy extension that enables some common extension for a standard
 * Digitas application, init some parameters and load config files.
 * 
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
class ConfigurationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (!isset($app['config_dir'])) {
            throw new \Exception('Undefined "config_dir" parameter');
        }
        
        if (!isset($app['env'])) {
            $app['env'] = null;
        }
        
        //load the config
        $loader = new YamlConfigLoader();
        $parameters = $loader->load($app['config_dir'], $app['env']);
        
        foreach($parameters as $groupName => $group) {
            foreach($group as $name => $parameter) {
                $app[$groupName . '.' . $name] = $parameter;
            }
        }
    }
}