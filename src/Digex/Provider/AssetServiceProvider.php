<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Digex\AssetGenerator;
use Digex\Extension\AssetExtension;

/**
 * @author Stéphane EL MANOUNI <stephane dot elmanouni at digitas dot fr>
 * @copyright Digitas France
 */
class AssetServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['asset_generator'] = $app->share(function($app) {
            return new AssetGenerator($app);
        });
        $app['asset_generator_extension'] = $app->share(function($app) {
            return new AssetExtension($app['asset_generator']);
        });
        $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
            $twig->addExtension(new AssetExtension($app['asset_generator_extension']));
            return $twig;
        }));
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registers
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }

}
