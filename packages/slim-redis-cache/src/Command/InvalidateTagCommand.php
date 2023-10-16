<?php

declare(strict_types=1);

namespace SlimRC\Command;

use SlimRC\Control\CacheControl;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvalidateTagCommand extends AbstractLoggingCommand
{
    protected function configure()
    {
        $this
            ->setName('slimrc:invalidate-tag')
            ->setDescription('Deletes entries for a given tag.')
            ->addArgument('tag', InputArgument::REQUIRED, 'The tag to delete related entries to.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $cacheControl = new CacheControl();
        $cacheControl->clear([$this->input->getArgument('tag')]);

        return Command::SUCCESS;
    }
}
