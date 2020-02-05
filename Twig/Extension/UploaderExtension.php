<?php

namespace Oneup\UploaderBundle\Twig\Extension;

use Oneup\UploaderBundle\Templating\Helper\UploaderHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploaderExtension extends AbstractExtension
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
        return [
            new TwigFunction('oneup_uploader_endpoint', [$this, 'endpoint']),
            new TwigFunction('oneup_uploader_progress', [$this, 'progress']),
            new TwigFunction('oneup_uploader_cancel', [$this, 'cancel']),
            new TwigFunction('oneup_uploader_upload_key', [$this, 'uploadKey']),
            new TwigFunction('oneup_uploader_maxsize', [$this, 'maxSize']),
        ];
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
