<?php

namespace Oneup\UploaderBundle\EventListener;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

class AllowedMimetypeAndExtensionValidationListener
{
    public function onValidate(ValidationEvent $event)
    {
        $config = $event->getConfig();
        $file = $event->getFile();

        if (empty($config['allowed_mimetypes'])) {
            return;
        }

        $mimetype = $file->getMimeType();
        $extension = strtolower($file->getExtension());

        if (!isset($config['allowed_mimetypes'][$mimetype])) {
            throw new ValidationException('error.whitelist');
        }

        if (empty($config['allowed_mimetypes'][$mimetype])
            || in_array($extension, $config['allowed_mimetypes'][$mimetype], true)
        ) {
            return;
        }

        throw new ValidationException('error.whitelist');
    }
}
