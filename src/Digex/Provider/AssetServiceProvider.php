<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Digex\AssetGenerator;
use Digex\Extension\AssetExtension;

use Digex\Console\Command\AssetsInstallCommand;

/**
 * @author    Stéphane EL MANOUNI <stephane dot elmanouni at digitas dot fr>
 * @copyright 2012 Digitas France
 */
class AssetServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['asset_extension'] = $app->share(function($app) {


            $dir = isset($app['asset.base_path'])?$app['asset.base_path']:$app['request']->getBasePath();

            return new AssetExtension($dir);
        });

        $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {



            $twig->addExtension($app['asset_extension']);

            return $twig;
        }));

        if(isset($app['console'])) {
            $app['console']->add(new AssetsInstallCommand());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {

    }
}
