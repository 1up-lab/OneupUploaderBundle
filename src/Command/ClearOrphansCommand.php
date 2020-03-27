<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Command;

use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearOrphansCommand extends Command
{
    protected static $defaultName = 'oneup:uploader:clear-orphans';

    /**
     * @var OrphanageManager
     */
    private $manager;

    public function __construct(OrphanageManager $manager)
    {
        $this->manager = $manager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Clear orphaned uploads according to the max-age you defined in your configuration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->manager->clear();

        return 0;
    }
}
