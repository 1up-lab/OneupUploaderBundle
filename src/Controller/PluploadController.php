<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PluploadController extends AbstractChunkedController
{
    public function upload(): JsonResponse
    {
        $request = $this->getRequest();
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);

        $chunked = null !== $request->get('chunks');

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

    protected function parseChunkedRequest(Request $request): array
    {
        $session = $request->getSession();

        $orig = $request->get('name');
        $index = (int) $request->get('chunk');
        $last = (int) $request->get('chunks') - 1 === (int) $request->get('chunk');

        // it is possible, that two clients send a file with the
        // exact same filename, therefore we have to add the session
        // to the uuid otherwise we will get a mess
        $uuid = md5(sprintf('%s.%s', $orig, $session->getId()));

        return [$last, $uuid, $index, $orig];
    }
}
