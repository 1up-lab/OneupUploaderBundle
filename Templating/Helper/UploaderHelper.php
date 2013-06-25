<?php

namespace Oneup\UploaderBundle\Templating\Helper;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\Helper;

class UploaderHelper extends Helper
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getName()
    {
        return 'oneup_uploader';
    }

    public function endpoint($key)
    {
        return $this->router->generate(sprintf('_uploader_upload_%s', $key));
    }

    public function progress($key)
    {
        return $this->router->generate(sprintf('_uploader_progress_%s', $key));
    }

    public function cancel($key)
    {
        return $this->router->generate(sprintf('_uploader_cancel_%s', $key));
    }

    public function uploadKey()
    {
        return ini_get('session.upload_progress.name');
    }
}
