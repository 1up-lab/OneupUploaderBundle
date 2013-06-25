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
        return array(
            'oneup_uploader_endpoint' => new \Twig_Function_Method($this, 'endpoint'),
            'oneup_uploader_progress_route' => new \Twig_Function_Method($this, 'progressRoute'),
            'oneup_uploader_progress_key' => new \Twig_Function_Method($this, 'progressKey')
        );
    }

    public function endpoint($key)
    {
        return $this->helper->endpoint($key);
    }

    public function progressRoute($key)
    {
        return $this->helper->progressRoute($key);
    }

    public function progressKey()
    {
        return $this->helper->progressKey();
    }
}
