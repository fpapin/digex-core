<?php

namespace Digex\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Silex\Application;

/**
 * @author Damien Pitard <dpitard at digitas dot fr>
 * @copyright Digitas France
 */
abstract class AppAwareCommand extends BaseCommand implements AppAwareCommandInterface
{
    protected $app;

    /**
     * Gets the Silex Application associated with this Console.
     *
     * @return Silex\Application
     */
    public function getApp()
    {
        return $this->app;
    }
    
    /**
     * Set the Silex Application associated to this Console
     * 
     * @param Silex\Application $app 
     */
    public function setApp(Application $app)
    {
        $this->app = $app;
    }
}