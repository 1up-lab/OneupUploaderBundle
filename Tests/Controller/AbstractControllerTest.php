<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected $client;
    protected $container;
    protected $createdFiles;
    
    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->helper = $this->container->get('oneup_uploader.templating.uploader_helper');
        $this->createdFiles = array();
        
        $routes = $this->container->get('router')->getRouteCollection()->all();
    }
    
    abstract protected function getConfigKey();
    abstract protected function getSingleRequestParameters();
    abstract protected function getSingleRequestFile();
    
    public function testSingleUpload()
    {
        // assemble a request
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $client->request('POST', $endpoint, $this->getSingleRequestParameters(), $this->getSingleRequestFile());
        $response = $client->getResponse();
        
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
    }
    
    public function testRoute()
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $this->assertNotNull($endpoint);
        $this->assertEquals(0, strpos('_uploader', $endpoint));
    }
    
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    public function testCallByGet()
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $this->client->request('GET', $endpoint);
    }
    
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    public function testCallByDelete()
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $this->client->request('DELETE', $endpoint);
    }
    
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    public function testCallByPut()
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        $this->client->request('PUT', $endpoint);
    }
    
    public function testCallByPost()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());
        
        $client->request('POST', $endpoint);
        $response = $client->getResponse();
        
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
    }
    
    protected function createTempFile($size = 128)
    {
        $file = tempnam(sys_get_temp_dir(), 'uploader_');
        file_put_contents($file, str_repeat('A', $size));
        
        $this->createdFiles[] = $file;
        
        return $file;
    }
    
    public function tearDown()
    {
        foreach($this->createdFiles as $file) {
            @unlink($file);
        }
    }
}
