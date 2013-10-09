<?php

namespace Oneup\UploaderBundle\EventListener;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

class DisallowedExtensionValidationListener
{
    public function onValidate(ValidationEvent $event)
    {
        $config = $event->getConfig();
        $file   = $event->getFile();

        $extension = $file->getExtension();

        if (count($config['disallowed_extensions']) > 0 && in_array($extension, $config['disallowed_extensions'])) {
            throw new ValidationException('error.blacklist');
        }
    }
}
