<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Digex\YamlConfigLoader;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
class ConfigurationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (!isset($app['env'])) {
            $app['env'] = null;
        }

        $app['config'] = $app->share(function() use ($app){
            
            if (!isset($app['config.config_dir'])) {
                throw new \RuntimeException('Undefined "config.config_dir" parameter');
            }

            $loader = new YamlConfigLoader();
            $parameters = $loader->load($app['config.config_dir'], $app['env']);

            return $parameters;
        });
    }
	
	public function boot(Application $app) {}
}