<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Tests\Controller;

use Oneup\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractControllerTest extends WebTestCase
{
    /**
     * @var array
     */
    protected $createdFiles;

    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var array
     */
    protected $requestHeaders;

    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @var UploaderHelper
     */
    protected $helper;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient(['debug' => false]);
        $this->client->catchExceptions(false);
        $this->client->disableReboot();

        /** @var ContainerInterface $container */
        $container = $this->client->getContainer();

        /** @var UploaderHelper $helper */
        $helper = $container->get('oneup_uploader.templating.uploader_helper');

        /** @var RouterInterface $router */
        $router = $container->get('router');

        self::$container = $container;

        $this->helper = $helper;
        $this->createdFiles = [];
        $this->requestHeaders = [
            'HTTP_ACCEPT' => 'application/json',
        ];

        $router->getRouteCollection()->all();
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $file) {
            @unlink($file);
        }

        foreach ($this->getUploadedFiles() as $file) {
            /* @var \SplFileInfo $file */
            @unlink($file->getPathname());
        }

        unset($this->client, $this->controller);
        static::$booted = false;
    }

    public function testRoute(): void
    {
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        $this->assertNotNull($endpoint);
        $this->assertSame(0, strpos($endpoint, '/_uploader'));
    }

    public function testCallByGet(): void
    {
        $this->implTestCallBy('GET', 405, 'text/html');
    }

    public function testCallByDelete(): void
    {
        $this->implTestCallBy('DELETE', 405, 'text/html');
    }

    public function testCallByPatch(): void
    {
        $this->implTestCallBy('PATCH');
    }

    public function testCallByPost(): void
    {
        $this->implTestCallBy('POST');
    }

    public function testCallByPut(): void
    {
        $this->implTestCallBy('PUT');
    }

    public function testEmptyHttpAcceptHeader(): void
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        // empty HTTP_ACCEPT header
        $client->request('POST', $endpoint, [], [], []);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame($response->headers->get('Content-Type'), 'text/plain; charset=UTF-8');
    }

    abstract protected function getConfigKey(): string;

    protected function implTestCallBy(string $method, int $expectedStatusCode = 200, string $expectedContentType = 'application/json'): void
    {
        $client = $this->client;
        $endpoint = $this->helper->endpoint($this->getConfigKey());

        if (405 === $expectedStatusCode) {
            $this->expectException(MethodNotAllowedHttpException::class);
        }

        $client->request($method, $endpoint, [], [], $this->requestHeaders);
        $response = $client->getResponse();

        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase($expectedContentType, (string) $response->headers->get('Content-Type'));
    }

    protected function createTempFile(int $size = 128): string
    {
        $file = (string) tempnam(sys_get_temp_dir(), 'uploader_');
        file_put_contents($file, str_repeat('A', $size));

        $this->createdFiles[] = $file;

        return $file;
    }

    protected function getUploadedFiles(): Finder
    {
        /** @var string $env */
        $env = self::$container->getParameter('kernel.environment');

        /** @var string $root */
        $root = self::$container->getParameter('kernel.project_dir');

        // assemble path
        $path = sprintf('%s/cache/%s/upload', $root, $env);

        $finder = new Finder();

        return $finder->in($path);
    }
}
