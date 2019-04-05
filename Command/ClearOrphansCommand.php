<?php

namespace Oneup\UploaderBundle\Command;

use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearOrphansCommand extends Command
{
    protected static $defaultName = 'oneup:uploader:clear-orphans'; // Make command lazy load

    /** @var OrphanageManager */
    protected $manager;

    public function __construct(OrphanageManager $manager, ?string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName) // BC with 2.7
            ->setDescription('Clear orphaned uploads according to the max-age you defined in your configuration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->manager->clear();
    }
}
