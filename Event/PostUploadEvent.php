<?php

namespace Oneup\UploaderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;

class PostUploadEvent extends Event
{
    protected $file;
    protected $request;
    protected $type;
    protected $response;
    protected $config;
    protected $generatedFileName;

    public function __construct($file, $generatedFileName, ResponseInterface $response, Request $request, $type, array $config)
    {
        $this->file = $file;
        $this->generatedFileName = $generatedFileName;
        $this->request = $request;
        $this->response = $response;
        $this->type = $type;
        $this->config = $config;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getConfig()
    {
        return $this->config;
    }

	public function getGeneratedFileName() {
		return $this->generatedFileName;
	}
	
	public function setGeneratedFileName($generatedFileName) {
		$this->generatedFileName = $generatedFileName;
		return $this;
	}
	
    
}
