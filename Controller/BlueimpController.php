<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class BlueimpController extends AbstractChunkedController
{
    public function upload()
    {
        $request = $this->getRequest();
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);

        $chunked = !is_null($request->headers->get('content-range'));

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

    public function progress()
    {
        $request = $this->getRequest();
        $session = $this->container->get('session');

        $prefix = ini_get('session.upload_progress.prefix');
        $name   = ini_get('session.upload_progress.name');

        // ref: https://github.com/blueimp/jQuery-File-Upload/wiki/PHP-Session-Upload-Progress
        $key = sprintf('%s.%s', $prefix, $request->get($name));
        $value = $session->get($key);

        $progress = array(
            'lengthComputable' => true,
            'loaded' => $value['bytes_processed'],
            'total' => $value['content_length']
        );

        return $this->createSupportedJsonResponse($progress);
    }

    protected function parseChunkedRequest(Request $request)
    {
        $session = $this->container->get('session');
        $headerRange = $request->headers->get('content-range');
        $attachmentName = rawurldecode(preg_replace('/(^[^"]+")|("$)/', '', $request->headers->get('content-disposition')));

        // split the header string to the appropriate parts
        list(, $startByte, $endByte, $totalBytes) = preg_split('/[^0-9]+/', $headerRange);

        // getting information about chunks
        // note: We don't have a chance to get the last $total
        // correct. This is due to the fact that the $size variable
        // is incorrect. As it will always be a higher number than
        // the one before, we just let that happen, if you have
        // any idea to fix this without fetching information about
        // previously saved files, let me know.
        $size  = ($endByte + 1 - $startByte);
        $last  = ($endByte + 1) == $totalBytes;
        $index = $last ? \PHP_INT_MAX : floor($startByte / $size);

        // it is possible, that two clients send a file with the
        // exact same filename, therefore we have to add the session
        // to the uuid otherwise we will get a mess
        $uuid  = md5(sprintf('%s.%s', $attachmentName, $session->getId()));
        $orig  = $attachmentName;

        return array($last, $uuid, $index, $orig);
    }
}
