<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Uploader\Response\FineUploaderResponse;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class FineUploaderController extends AbstractChunkedController
{
    public function upload(): JsonResponse
    {
        $request = $this->getRequest();

        /** @var TranslatorInterface $translator */
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
                $response->setError($translator->trans($e->getMessage(), [], 'OneupUploaderBundle'));

                $this->errorHandler->addException($response, $e);

                // an error happended, return this error message.
                return $this->createSupportedJsonResponse($response->assemble());
            }
        }

        return $this->createSupportedJsonResponse($response->assemble());
    }

    protected function parseChunkedRequest(Request $request): array
    {
        $index = (int) $request->get('qqpartindex');
        $total = (int) $request->get('qqtotalparts');
        $uuid = $request->get('qquuid');
        $orig = $request->get('qqfilename');
        $last = ($total - 1) === $index;

        return [$last, $uuid, $index, $orig];
    }
}
