<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\EventListener;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

class DisallowedMimetypeValidationListener
{
    public function onValidate(ValidationEvent $event): void
    {
        $config = $event->getConfig();
        $file = $event->getFile();

        if (0 === \count($config['disallowed_mimetypes'])) {
            return;
        }

        $mimetype = $file->getExtension();

        if (\in_array($mimetype, $config['disallowed_mimetypes'], true)) {
            throw new ValidationException('error.blacklist');
        }
    }
}
