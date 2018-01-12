<?php

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;
use Symfony\Component\Translation\TranslatorInterface;

class BlueimpErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function addException(AbstractResponse $response, Exception $exception)
    {
        $message = $this->translator->trans($exception->getMessage(), [], 'OneupUploaderBundle');
        $response->addToOffset(['error' => $message], ['files']);
    }
}
