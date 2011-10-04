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
 */
class ConfigurationServiceProvider implements ServiceProviderInterface
{
    protected $configDir;
    
    public function __construct($configDir)
    {
        $this->configDir = $configDir;
    }
    
    public function register(Application $app)
    {
        //load the config
        $loader = new YamlConfigLoader();
        $parameters = $loader->load($this->configDir);
        
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