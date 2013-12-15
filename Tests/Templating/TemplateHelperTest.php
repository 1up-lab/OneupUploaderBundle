<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Chunk;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TemplateHelperTest extends WebTestCase
{
    public function testName()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $helper = $container->get('oneup_uploader.templating.uploader_helper');

        // this is for code coverage
        $this->assertEquals($helper->getName(), 'oneup_uploader');
    }

    public function testNonExistentMappingForMaxSize()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $client = static::createClient();
        $container = $client->getContainer();

        $helper = $container->get('oneup_uploader.templating.uploader_helper');
        $helper->maxSize(uniqid());

        $this->fail('No exception has been raised');
    }
}
