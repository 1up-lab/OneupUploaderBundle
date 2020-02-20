<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Twig\Extension;

use Oneup\UploaderBundle\Templating\Helper\UploaderHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploaderExtension extends AbstractExtension
{
    /**
     * @var UploaderHelper
     */
    protected $helper;

    public function __construct(UploaderHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getName(): string
    {
        return 'oneup_uploader';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('oneup_uploader_endpoint', [$this, 'endpoint']),
            new TwigFunction('oneup_uploader_progress', [$this, 'progress']),
            new TwigFunction('oneup_uploader_cancel', [$this, 'cancel']),
            new TwigFunction('oneup_uploader_upload_key', [$this, 'uploadKey']),
            new TwigFunction('oneup_uploader_maxsize', [$this, 'maxSize']),
        ];
    }

    public function endpoint(string $key): string
    {
        return $this->helper->endpoint($key);
    }

    public function progress(string $key): string
    {
        return $this->helper->progress($key);
    }

    public function cancel(string $key): string
    {
        return $this->helper->cancel($key);
    }

    public function uploadKey(): string
    {
        return $this->helper->uploadKey();
    }

    /**
     * @return mixed
     */
    public function maxSize(string $key)
    {
        return $this->helper->maxSize($key);
    }
}
