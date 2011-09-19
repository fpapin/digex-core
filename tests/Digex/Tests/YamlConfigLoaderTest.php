<?php

namespace Digex\Tests;

use Digex\YamlConfigLoader;

/**
 * YamlConfigLoaderTest
 *
 * @author Damien Pitard <dpitard at digitas.fr>
 */
class YamlConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    function testLoad()
    {
        $loader = new YamlConfigLoader();
        $params = $loader->load(__DIR__ . '/../../fixtures/config');
        
        $this->assertFalse($params['group1']['param1']);
        $this->assertFalse($params['group1']['param2']['param2-1']);
        
    }
    
    function testLoadOverride()
    {
        putenv('ENV=dev');
        $loader = new YamlConfigLoader();
        $params = $loader->load(__DIR__ . '/../../fixtures/config');
        
        $this->assertTrue($params['group1']['param1']);
        $this->assertTrue($params['group1']['param2']['param2-1']);
    }
}
