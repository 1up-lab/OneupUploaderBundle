<?php

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Uploader\Exception\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Uploader\Response\FineUploaderResponse;
use Oneup\UploaderBundle\Uploader\File\FileInterface;

class FineUploaderController extends AbstractChunkedController
{
    public function upload()
    {
        $request    = $this->getRequest();
        $translator = $this->container->get('translator');

        $response   = new FineUploaderResponse();
        $totalParts = $request->get('qqtotalparts', 1);
        $files      = $this->getFiles($request->files);
        $chunked    = $totalParts > 1;

        foreach ($files as $file) {
            try {
                $chunked ?
                    $this->handleChunkedUpload($file, $response, $request) :
                    $this->handleUpload($file, $response, $request)
                ;
            }
            //Distinguish Validation exception from the general upload exception,
            //so we can set the preventRetry option for the frontend
            catch(ValidationException $e) {
                $response->setSuccess(false);
                //setting the second param to true will prevent retrying the upload
                $response->setError($translator->trans($e->getMessage(), array(), 'OneupUploaderBundle'), true);

                $this->errorHandler->addException($response, $e);

                // an error happened, return this error message.
                return $this->createSupportedJsonResponse($response->assemble());
            }
            catch (UploadException $e) {
                $response->setSuccess(false);
                $response->setError($translator->trans($e->getMessage(), array(), 'OneupUploaderBundle'));

                $this->errorHandler->addException($response, $e);

                // an error happened, return this error message.
                return $this->createSupportedJsonResponse($response->assemble());
            }
        }

        return $this->createSupportedJsonResponse($response->assemble());
    }

    /**
     * POST 200-204
     */
    public function chunkingSuccess()
    {
        $request    = $this->getRequest();
        $translator = $this->container->get('translator');
        $response   = new FineUploaderResponse();

        // get basic container stuff
        $chunkManager = $this->container->get('oneup_uploader.chunk_manager');

        try {
            // get information about this chunked request
            list($last, $uuid, $index, $orig) = $this->parseChunkedRequest($request);

            /** @var FileInterface $assembled  */

            if (!$chunkManager->getLoadDistribution()) {
                $chunks = $chunkManager->getChunks($uuid);
                $assembled = $chunkManager->assembleChunks($chunks, true, true);
            }

            $path = $assembled->getPath();

            $this->handleUpload($assembled, $response, $request);

            $chunkManager->cleanup($path);
        }
        catch(UploadException $e) {
            $response->setSuccess(false);
            $response->setError($translator->trans($e->getMessage(), array(), 'OneupUploaderBundle'));

            $this->errorHandler->addException($response, $e);
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
