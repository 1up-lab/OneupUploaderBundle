<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class BlueimpErrorHandler implements ErrorHandlerInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function addException(AbstractResponse $response, \Exception $exception): void
    {
        $message = $this->translator->trans($exception->getMessage(), [], 'OneupUploaderBundle');
        $response->addToOffset(['error' => $message], ['files']);
    }
}
