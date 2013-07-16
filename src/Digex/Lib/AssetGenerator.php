<?php

namespace Asset\Lib;

/**
 * @author Stéphane EL MANOUNI <stephane dot elmanouni at digitas dot fr>
 * @copyright Digitas France
 */
class AssetGenerator {

    private $app;

    function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * If a directory is set in config.yml, direcotry is concatenate to $url
     *
     * @param $url string, path of the asset
     * @return string
     */
    public function asset($url)
    {
        $assetDir = isset($this->app['config']['asset']['directory']) ?
            $this->app['config']['asset']['directory'] :
            $this->app['request']->getBasePath();

        return sprintf('%s/%s', $assetDir, ltrim($url, '/'));
    }

}
