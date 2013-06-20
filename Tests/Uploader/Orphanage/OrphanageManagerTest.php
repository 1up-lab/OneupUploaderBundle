<?php

namespace Oneup\UploaderBundle\Tests\Uploader\Orphanage;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager;

class OrphanageManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $numberOfOrphans;
    protected $orphanagePath;
    protected $mockContainer;
    protected $mockConfig;

    public function setUp()
    {
        $this->numberOfOrphans = 10;
        $this->orphanagePath = sys_get_temp_dir() . '/orphanage';

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->orphanagePath);

        // create n orphans with a filemtime in the past
        for ($i = 0; $i < $this->numberOfOrphans; $i ++) {
            touch($this->orphanagePath . '/' . uniqid(), time() - 1000);
        }

        $this->mockConfig = array(
            'maxage' => 100,
            'directory' => $this->orphanagePath
        );

        $this->mockContainer = $this->getContainerMock();
    }

    public function testGetSpecificService()
    {
        $manager = new OrphanageManager($this->mockContainer, $this->mockConfig);
        $service = $manager->get('grumpycat');

        $this->assertTrue($service);
    }

    public function testClearAllInPast()
    {
        // create n orphans with a filemtime in the past
        for ($i = 0; $i < $this->numberOfOrphans; $i ++) {
            touch($this->orphanagePath . '/' . uniqid(), time() - 1000);
        }

        $manager = new OrphanageManager($this->mockContainer, $this->mockConfig);
        $manager->clear();

        $finder = new Finder();
        $finder->in($this->orphanagePath)->files();

        $this->assertCount(0, $finder);
    }

    public function testClearSomeInPast()
    {
        // create n orphans with half filetimes in the past and half in the future
        // relative to the given threshold
        for ($i = 0; $i < $this->numberOfOrphans; $i ++) {
            touch($this->orphanagePath . '/' . uniqid(), time() - $i * 20);
        }

        $manager = new OrphanageManager($this->mockContainer, $this->mockConfig);
        $manager->clear();

        $finder = new Finder();
        $finder->in($this->orphanagePath)->files();

        $this->assertCount($this->numberOfOrphans / 2, $finder);
    }

    public function testClearIfDirectoryDoesNotExist()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->mockConfig['directory']);

        $manager = new OrphanageManager($this->mockContainer, $this->mockConfig);
        $manager->clear();

        // yey, no exception
        $this->assertTrue(true);
    }

    protected function getContainerMock()
    {
        $mock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $mock
            ->expects($this->any())
            ->method('get')
            ->with('oneup_uploader.orphanage.grumpycat')
            ->will($this->returnValue(true))
        ;

        return $mock;
    }

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->orphanagePath);
    }
}
