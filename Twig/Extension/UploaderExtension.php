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
            'oneup_uploader_endpoint' => new \Twig_Function_Method($this, 'endpoint')
            'oneup_uploader_progress' => new \Twig_Function_Method($this, 'progress')
        );
    }

    public function endpoint($key)
    {
        return $this->helper->endpoint($key);
    }

    public function progress($key)
    {
        return $this->helper->progress($key);
    }
}
