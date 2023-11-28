<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Templating\Helper;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\Helper;

class UploaderHelper extends Helper
{
    public function __construct(protected RouterInterface $router, protected array $maxsize)
    {
    }

    public function getName(): string
    {
        return 'oneup_uploader';
    }

    public function endpoint(string $key): string
    {
        return $this->router->generate(sprintf('_uploader_upload_%s', $key));
    }

    public function progress(string $key): string
    {
        return $this->router->generate(sprintf('_uploader_progress_%s', $key));
    }

    public function cancel(string $key): string
    {
        return $this->router->generate(sprintf('_uploader_cancel_%s', $key));
    }

    public function uploadKey(): string
    {
        return (string) \ini_get('session.upload_progress.name');
    }

    /**
     * @return int
     */
    public function maxSize(string $key)
    {
        if (!\array_key_exists($key, $this->maxsize)) {
            throw new \InvalidArgumentException('No such mapping found to get maxsize for.');
        }

        return $this->maxsize[$key];
    }
}
