<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\EventListener;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

class MaxSizeValidationListener
{
    public function onValidate(ValidationEvent $event): void
    {
        $config = $event->getConfig();
        $file = $event->getFile();

        if ($file->getSize() > $config['max_size']) {
            throw new ValidationException('error.maxsize');
        }
    }
}
