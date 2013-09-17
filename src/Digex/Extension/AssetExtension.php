<?php

namespace Digex\Extension;

/**
 * @author    Stéphane EL MANOUNI <stephane dot elmanouni at digitas dot fr>
 * @author    Damien Pitard <damien.pitard@digitas.fr>
 * @copyright 2012 Digitas France
 */
class AssetExtension extends  \Twig_Extension
{
    private $dir;

    /**
     * [__construct description]
     * @param [type] $dir [description]
     */
    function __construct($dir)
    {
        $this->dir = $dir;
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
        return sprintf('%s/%s', $this->dir, ltrim($url, '/'));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'asset';
    }
}
