<?php

namespace Digex\Console\Command;

use Silex\Application;

/**
 * @author Damien Pitard <dpitard at digitas dot fr>
 * @copyright Digitas France
 */
interface AppAwareCommandInterface
{
    public function getApp();
    
    public function setApp(Application $app);
}