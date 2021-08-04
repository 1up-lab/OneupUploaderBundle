<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BlueimpController extends AbstractChunkedController
{
    public function upload(): JsonResponse
    {
        $request = $this->getRequest();
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);

        $chunked = null !== $request->headers->get('content-range');

        foreach ((array) $files as $file) {
            try {
                $chunked ?
                    $this->handleChunkedUpload($file, $response, $request) :
                    $this->handleUpload($file, $response, $request)
                ;
            } catch (UploadException $e) {
                $this->errorHandler->addException($response, $e);
            }
        }

        return $this->createSupportedJsonResponse($response->assemble());
    }

    public function progress(): JsonResponse
    {
        $request = $this->getRequest();

        $session = $request->getSession();

        $prefix = (string) ini_get('session.upload_progress.prefix');
        $name = (string) ini_get('session.upload_progress.name');

        // ref: https://github.com/blueimp/jQuery-File-Upload/wiki/PHP-Session-Upload-Progress
        $key = sprintf('%s.%s', $prefix, $request->get($name));
        $value = $session->get($key);

        $progress = [
            'lengthComputable' => true,
            'loaded' => $value['bytes_processed'],
            'total' => $value['content_length'],
        ];

        return $this->createSupportedJsonResponse($progress);
    }

    protected function parseChunkedRequest(Request $request): array
    {
        $session = $request->getSession();
        $headerRange = $request->headers->get('content-range');
        $attachmentName = rawurldecode((string) preg_replace('/(^[^"]+")|("$)/', '', (string) $request->headers->get('content-disposition')));

        // split the header string to the appropriate parts
        [, $startByte, $endByte, $totalBytes] = preg_split('/[^0-9]+/', (string) $headerRange);

        // getting information about chunks
        // note: We don't have a chance to get the last $total
        // correct. This is due to the fact that the $size variable
        // is incorrect. As it will always be a higher number than
        // the one before, we just let that happen, if you have
        // any idea to fix this without fetching information about
        // previously saved files, let me know.
        $size = ((int) $endByte + 1 - (int) $startByte);
        $last = ((int) $endByte + 1) === (int) $totalBytes;
        $index = $last ? \PHP_INT_MAX : (int) floor($startByte / $size);

        // it is possible, that two clients send a file with the
        // exact same filename, therefore we have to add the session
        // to the uuid otherwise we will get a mess
        $uuid = md5(sprintf('%s.%s', $attachmentName, $session->getId()));
        $orig = $attachmentName;

        return [$last, $uuid, $index, $orig];
    }
}
