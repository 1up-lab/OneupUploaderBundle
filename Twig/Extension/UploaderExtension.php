<?php

namespace Oneup\UploaderBundle\Twig\Extension;

use Oneup\UploaderBundle\Templating\Helper\UploaderHelper;

class UploaderExtension extends \Twig_Extension
{
    protected $helper;

    public function __construct(UploaderHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getName()
    {
        return 'oneup_uploader';
    }

    public function getFunctions()
    {
        return array('oneup_uploader_endpoint' => new \Twig_Function_Method($this, 'endpoint'));
    }

    public function endpoint($key)
    {
        return $this->helper->endpoint($key);
    }
}
