<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Digex\Console\Console;
use Digex\Console\Command\VersionCommand;
use Digex\Console\Command\VendorCommand;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['console'] = $app->share(function () use ($app) {
            return new Console($app);
        });
        
        $app['console']->add(new VersionCommand());
        $app['console']->add(new VendorCommand());
    }
}