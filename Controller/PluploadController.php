<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class PluploadController extends AbstractChunkedController
{
    public function upload()
    {
        $request = $this->getRequest();
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);

        $chunked = !is_null($request->get('chunks'));

        foreach ($files as $file) {
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

    protected function parseChunkedRequest(Request $request)
    {
        $session = $this->container->get('session');

        $orig  = $request->get('name');
        $index = $request->get('chunk');
        $last  = $request->get('chunks') - 1 == $request->get('chunk');

        // it is possible, that two clients send a file with the
        // exact same filename, therefore we have to add the session
        // to the uuid otherwise we will get a mess
        $uuid = md5(sprintf('%s.%s', $orig, $session->getId()));

        return array($last, $uuid, $index, $orig);
    }
}
