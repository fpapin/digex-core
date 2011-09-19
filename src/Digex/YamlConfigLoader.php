<?php

namespace Digex;

use Symfony\Component\Yaml\Yaml;

/**
 * YamlConfigLoader
 *
 * @author Damien Pitard <dpitard at digitas.fr>
 */
class YamlConfigLoader
{
    /**
     * Load the config
     * 
     * @param string $dir
     * @param string $basename
     * @param string $extension
     * @return array 
     */
    public function load($dir, $basename = 'config', $extension = 'yml')
    {
        $filepath = $dir . '/' . $basename . '.' . $extension;

        if (!file_exists($filepath)) {
            throw new \Exception(sprintf("Config file \"%s\" does not exist", $filepath));
        }

        $parameters = Yaml::parse($filepath);

        //Override configuration for a specific environment
        $env = getenv('ENV');
        $filepath = $dir . '/' . $basename . '_' . $env . '.' . $extension;
        if (false !== $env & file_exists($filepath)) {
            $envParameters = Yaml::parse($filepath);
            if ($envParameters) {
                $parameters = $this->deepMerge($parameters, $envParameters);
            }
        }
        
        return $parameters;
    }
    
    /**
     * Do a deep merge of two arrays
     * 
     * @param array $leftSide
     * @param array $rightSide
     * @return array 
     */
    protected function deepMerge($leftSide, $rightSide)
    {
        if (!is_array($rightSide)) {
            
            return $rightSide;
        }
        
        foreach ($rightSide as $k => $v) {
            // no conflict
            if (!array_key_exists($k, $leftSide)) {

                $leftSide[$k] = $v;
                continue;
            }
            
            $leftSide[$k] = $this->deepMerge($leftSide[$k], $v);
        }
        
        return $leftSide;
    }
}
