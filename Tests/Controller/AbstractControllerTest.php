<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;

abstract class AbstractControllerTest extends WebTestCase
{
    protected $createdFiles;

    /**
     * @var Client
     */
    protected $client;
    protected $container;
    protected $requestHeaders;

    /**
     * @var UploaderHelper
     */
    protected $helper;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->helper = $this->container->get('oneup_uploader.templating.uploader_helper');
        $this->createdFiles = [];
        $this->requestHeaders = [
            'HTTP_ACCEPT' => 'application/json',
        ];

        $this->container->get('router')->getRouteCollection()->all();
    }

    public function tearDown()
    {
        foreach ($this->createdFiles as $file) {
            @unlink($file);
        }

        foreach ($this->getUploadedFiles() as $file) {
            @unlink($file);
        }

        unset($this->client, $this->controller);
    }

    public function testRoute()
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $this->assertNotNull($endpoint);
        $this->assertSame(0, strpos($endpoint, '/_uploader'));
    }

    public function testCallByGet()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class);

        $this->implTestCallBy('GET');
    }

    public function testCallByDelete()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class);

        $this->implTestCallBy('DELETE');
    }

    public function testCallByPatch()
    {
        $this->implTestCallBy('PATCH');
    }

    public function testCallByPost()
    {
        $this->implTestCallBy('POST');
    }

    public function testCallByPut()
    {
        $this->implTestCallBy('PUT');
    }

    public function testEmptyHttpAcceptHeader()
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        // empty HTTP_ACCEPT header
        $client->request('POST', $endpoint, [], [], []);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'text/plain; charset=UTF-8');
    }

    abstract protected function getConfigKey();

    protected function implTestCallBy($method)
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $client->request($method, $endpoint, [], [], $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'application/json');
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
        $env = $this->container->getParameter('kernel.environment');
        $root = $this->container->getParameter('kernel.root_dir');

        // assemble path
        $path = sprintf('%s/cache/%s/upload', $root, $env);

        $finder = new Finder();
        $files = $finder->in($path);

        return $files;
    }
}
