<?php

namespace Digex;

use Silex\Application as SilexApplication;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Digex\Provider\LazyRegisterServiceProvider;

/**
 * @author Damien Pitard <dpitard at digitas dot fr>
 * @copyright Digitas France
 */
abstract class StandardControllerProvider implements ControllerProviderInterface, ServiceProviderInterface
{
    public function register(SilexApplication $app)
    {
        $app->register(new LazyRegisterServiceProvider());
    }
}
