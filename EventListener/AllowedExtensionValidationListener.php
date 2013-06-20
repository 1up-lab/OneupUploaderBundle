<?php

namespace Oneup\UploaderBundle\EventListener;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

class AllowedExtensionValidationListener
{
    public function onValidate(ValidationEvent $event)
    {
        $config = $event->getConfig();
        $file   = $event->getFile();

        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        if (count($config['allowed_extensions']) > 0 && !in_array($extension, $config['allowed_extensions'])) {
            throw new ValidationException('error.whitelist');
        }
    }
}
