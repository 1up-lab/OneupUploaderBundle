<?php

namespace Oneup\UploaderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearChunkCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oneup:uploader:clear-chunks')
            ->setDescription('Clear chunks according to the max-age you defined in your configuration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('oneup_uploader.chunk_manager');
        $manager->clear();
    }
}
