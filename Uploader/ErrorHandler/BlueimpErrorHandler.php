<?php

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\ErrorHandler\ErrorHandlerInterface;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class BlueimpErrorHandler implements ErrorHandlerInterface
{
    private $translator;
    
    public function __construct(TranslatorInterface $translator) {
    	$this->translator = $translator;
    }
	
	public function addException(AbstractResponse $response, Exception $exception)
    {
        $message = $exception->getMessage();
        $message = $this->translator->trans($message);
        $response->addToOffset(array('error' => $message), array('files'));
    }
}
