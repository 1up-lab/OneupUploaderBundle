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
            'oneup_uploader_endpoint'   => new \Twig_Function_Method($this, 'endpoint'),
            'oneup_uploader_progress'   => new \Twig_Function_Method($this, 'progress'),
            'oneup_uploader_cancel'     => new \Twig_Function_Method($this, 'cancel'),
            'oneup_uploader_upload_key' => new \Twig_Function_Method($this, 'uploadKey'),
            'oneup_uploader_maxsize'    => new \Twig_Function_Method($this, 'maxSize'),
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

    public function cancel($key)
    {
        return $this->helper->cancel($key);
    }

    public function uploadKey()
    {
        return $this->helper->uploadKey();
    }

    public function maxSize($key)
    {
        return $this->helper->maxSize($key);
    }
}
