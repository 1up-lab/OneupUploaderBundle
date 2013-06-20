<?php

namespace Oneup\UploaderBundle\EventListener;

use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;

class DisallowedMimetypeValidationListener
{
    public function onValidate(ValidationEvent $event)
    {
        $config = $event->getConfig();
        $file   = $event->getFile();

        if (count($config['disallowed_mimetypes']) == 0) {
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $file->getRealpath());

        if (in_array($mimetype, $config['disallowed_mimetypes'])) {
            throw new ValidationException('error.blacklist');
        }
    }
}
