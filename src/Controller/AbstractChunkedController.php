<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Event\PostChunkUploadEvent;
use Oneup\UploaderBundle\Uploader\Chunk\ChunkManagerInterface;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractChunkedController extends AbstractController
{
    /**
     *  Parses a chunked request and return relevant information.
     *
     *  This function must return an array containing the following
     *  keys and their corresponding values:
     *    - last: Wheter this is the last chunk of the uploaded file
     *    - uuid: A unique id which distinguishes two uploaded files
     *            This uuid must stay the same among the task of
     *            uploading a chunked file.
     *    - index: A numerical representation of the currently uploaded
     *            chunk. Must be higher that in the previous request.
     *    - orig: The original file name.
     *
     * @param Request $request - The request object
     */
    abstract protected function parseChunkedRequest(Request $request): array;

    /**
     *  This function will be called in order to upload and save an
     *  uploaded chunk.
     *
     *  This function also calls the chunk manager if the function
     *  parseChunkedRequest has set true for the "last" key of the
     *  returned array to reassemble the uploaded chunks.
     *
     * @param UploadedFile      $file     - The uploaded chunk
     * @param responseInterface $response - A response object
     * @param Request           $request  - The request object
     */
    protected function handleChunkedUpload(UploadedFile $file, ResponseInterface $response, Request $request): void
    {
        /** @var ChunkManagerInterface $chunkManager */
        $chunkManager = $this->container->get('oneup_uploader.chunk_manager');

        // get information about this chunked request
        [$last, $uuid, $index, $orig] = $this->parseChunkedRequest($request);
        $chunk = $chunkManager->addChunk($uuid, $index, $file, $orig);

        if (null !== $chunk) {
            $this->dispatchChunkEvents($chunk, $response, $request, $last);
        }

        if ($chunkManager->getLoadDistribution()) {
            $chunks = $chunkManager->getChunks($uuid);
            $assembled = $chunkManager->assembleChunks($chunks, true, $last);

            if (null === $chunk) {
                $this->dispatchChunkEvents($assembled, $response, $request, $last);
            }
        } else {
            $chunks = $chunkManager->getChunks($uuid);
            $assembled = $chunkManager->assembleChunks($chunks, true, true);
        }

        // if all chunks collected and stored, proceed
        // with reassembling the parts
        if ($last) {
            $path = $assembled->getPath();
            $this->handleUpload($assembled, $response, $request);

            $chunkManager->cleanup($path);
        }
    }

    /**
     *  This function is a helper function which dispatches post chunk upload event.
     *
     * @param mixed             $uploaded - The uploaded chunk
     * @param responseInterface $response - A response object
     * @param Request           $request  - The request object
     * @param bool              $isLast   - True if this is the last chunk, false otherwise
     */
    protected function dispatchChunkEvents($uploaded, ResponseInterface $response, Request $request, $isLast): void
    {
        // dispatch post upload event (both the specific and the general)
        $postUploadEvent = new PostChunkUploadEvent($uploaded, $response, $request, $isLast, $this->type, $this->config);

        $this->dispatchEvent($postUploadEvent, PostChunkUploadEvent::NAME);
    }
}
