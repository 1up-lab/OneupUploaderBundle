<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\Response\FineUploaderResponse;

class FineUploaderController extends AbstractChunkedController
{
    public function upload()
    {
        $request = $this->getRequest();
        $translator = $this->container->get('translator');

        $response = new FineUploaderResponse();
        $totalParts = $request->get('qqtotalparts', 1);
        $files = $this->getFiles($request->files);
        $chunked = $totalParts > 1;

        foreach ($files as $file) {
            try {
                $chunked ?
                    $this->handleChunkedUpload($file, $response, $request) :
                    $this->handleUpload($file, $response, $request)
                ;
            } catch (UploadException $e) {
                $response->setSuccess(false);
                $response->setError($translator->trans($e->getMessage(), array(), 'OneupUploaderBundle'));

                $this->errorHandler->addException($response, $e);

                // an error happended, return this error message.
                return $this->createSupportedJsonResponse($response->assemble());
            }
        }

        return $this->createSupportedJsonResponse($response->assemble());
    }

    protected function parseChunkedRequest(Request $request)
    {
        $index = $request->get('qqpartindex');
        $total = $request->get('qqtotalparts');
        $uuid  = $request->get('qquuid');
        $orig  = $request->get('qqfilename');
        $last  = ($total - 1) == $index;

        return array($last, $uuid, $index, $orig);
    }
}
