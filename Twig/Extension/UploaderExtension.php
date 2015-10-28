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
            new \Twig_SimpleFunction('oneup_uploader_endpoint', array($this, 'endpoint')),
            new \Twig_SimpleFunction('oneup_uploader_progress', array($this, 'progress')),
            new \Twig_SimpleFunction('oneup_uploader_cancel', array($this, 'cancel')),
            new \Twig_SimpleFunction('oneup_uploader_upload_key', array($this, 'uploadKey')),
            new \Twig_SimpleFunction('oneup_uploader_maxsize', array($this, 'maxSize')),
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
