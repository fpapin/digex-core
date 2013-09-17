<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Digex\Console\Console;

/**
 * ConsoleServiceProvider.
 *
 * @author    Damien Pitard <dpitard at digitas.fr>
 * @copyright 2012 Digitas France
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['console'] = $app->share(function ($app) {

            return new Console($app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {

    }
}