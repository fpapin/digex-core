<?php

namespace Digex;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
class Digex
{
    const VERSION = '@package_version@';
    
    /**
     * Get the build version of this Digex package
     * 
     * @return string
     */
    public static function getVersion()
    {
        if (self::VERSION === '@package_version@') {
            return 'UNKNOWN';
        } else {
            return self::VERSION;
        }
    }
}
