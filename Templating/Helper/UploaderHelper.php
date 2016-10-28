<?php

namespace Oneup\UploaderBundle\Templating\Helper;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\Helper;

class UploaderHelper extends Helper
{
    protected $router;
    protected $maxsize;
    protected $maxchunksize;

    public function __construct(RouterInterface $router, array $maxsize, array $maxchunksize)
    {
        $this->router       = $router;
        $this->maxsize      = $maxsize;
        $this->maxchunksize = $maxchunksize;
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

    public function maxSize($key)
    {
        if (!array_key_exists($key, $this->maxsize)) {
            throw new \InvalidArgumentException('No such mapping found to get maxsize for.');
        }

        return $this->maxsize[ $key ];
    }

    public function maxChunkSize($key)
    {
        if (!array_key_exists($key, $this->maxchunksize)) {
            throw new \InvalidArgumentException('No such mapping found to get maxchunksize for.');
        }

        return $this->maxchunksize[ $key ];
    }

    public function chunkingSuccess($key)
    {
        return $this->router->generate(sprintf('_uploader_chunking_success_%s', $key));
    }
}
