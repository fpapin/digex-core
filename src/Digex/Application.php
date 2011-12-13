<?php

namespace Digex;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
abstract class Application
{
    protected $app;
    
    public function __construct($env = null, $debug = true)
    {
        $this->app = new SilexApplication();
        
        $this->app['debug'] = $debug;
        $this->app['env'] = $env;
        
         $this->configure($this->app);
    }
    
    public function run()
    {
        $this->app->run();
    }
    
    abstract public function configure(SilexApplication $app);
}
