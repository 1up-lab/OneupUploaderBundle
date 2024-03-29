<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class NoopErrorHandler implements ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, \Exception $exception): void
    {
        // noop
    }
}
