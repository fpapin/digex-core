<?php

namespace Asset\Extension;

/**
 * @author Stéphane EL MANOUNI <stephane dot elmanouni at digitas dot fr>
 * @copyright Digitas France
 */
class AssetExtension extends  \Twig_Extension
{
    private $assetManager;

    function __construct($assetManager)
    {
        $this->assetManager = $assetManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'asset'    => new \Twig_Function_Method($this, 'asset'),
        );
    }

    /**
     * @param $url
     * @return mixed
     */
    public function asset($url)
    {
        return $this->assetManager->asset($url);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'dilex_asset';
    }
}
