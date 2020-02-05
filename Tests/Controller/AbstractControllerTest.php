<?php

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

abstract class AbstractControllerTest extends WebTestCase
{
    protected $createdFiles;

    /**
     * @var Client
     */
    protected $client;
    protected $requestHeaders;
    protected static $container;

    /**
     * @var UploaderHelper
     */
    protected $helper;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        self::$container = $this->client->getContainer();
        $this->helper = self::$container->get('oneup_uploader.templating.uploader_helper');
        $this->createdFiles = [];
        $this->requestHeaders = [
            'HTTP_ACCEPT' => 'application/json',
        ];

        self::$container->get('router')->getRouteCollection()->all();
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
        $this->implTestCallBy('GET', 405, 'text/html');
    }

    public function testCallByDelete()
    {
        $this->implTestCallBy('DELETE', 405, 'text/html');
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

    protected function implTestCallBy($method, $expectedStatusCode = 200, $expectedContentType = 'application/json')
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        if (405 === $expectedStatusCode) {
            $this->expectException(MethodNotAllowedHttpException::class);
        }

        $client->request($method, $endpoint, [], [], $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertContains($expectedContentType, $response->headers->get('Content-Type'));
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
        $env = self::$container->getParameter('kernel.environment');
        $root = self::$container->getParameter('kernel.root_dir');

        // assemble path
        $path = sprintf('%s/cache/%s/upload', $root, $env);

        $finder = new Finder();

        return $finder->in($path);
    }
}
