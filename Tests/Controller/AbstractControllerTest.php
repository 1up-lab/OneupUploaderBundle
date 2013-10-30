<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected $createdFiles;
    protected $client;
    protected $container;
    protected $requestHeaders;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->helper = $this->container->get('oneup_uploader.templating.uploader_helper');
        $this->createdFiles = array();
        $this->requestHeaders = array(
            'HTTP_ACCEPT' => 'application/json'
        );

        $this->container->get('router')->getRouteCollection()->all();
    }

    abstract protected function getConfigKey();

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

        $client->request('POST', $endpoint, array(), array(), $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'application/json');
    }

    public function testEmptyHttpAcceptHeader()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        // empty HTTP_ACCEPT header
        $client->request('POST', $endpoint, array(), array(), array());
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->headers->get('Content-Type'), 'text/plain; charset=UTF-8');
    }

    protected function createTempFile($size = 128)
    {
        $file = tempnam(sys_get_temp_dir(), 'uploader_');
        file_put_contents($file, str_repeat('A', $size));

        $this->createdFiles[] = $file;

        return $file;
    }

    protected function getUploadedFiles()
    {
        $env  = $this->container->getParameter('kernel.environment');
        $root = $this->container->getParameter('kernel.root_dir');

        // assemble path
        $path = sprintf('%s/cache/%s/upload', $root, $env);

        $finder = new Finder();
        $files  = $finder->in($path);

        return $files;
    }

    public function tearDown()
    {
        foreach ($this->createdFiles as $file) {
            @unlink($file);
        }

        foreach ($this->getUploadedFiles() as $file) {
            @unlink($file);
        }

        unset($this->client);
        unset($this->controller);
    }
}
