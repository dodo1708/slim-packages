<?php

declare(strict_types=1);

namespace SlimRC\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractLoggingCommand extends Command
{
    protected InputInterface $input;

    protected ?OutputInterface $output = null;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->setOutput($output);
        return self::SUCCESS;
    }

    public function setOutput(?OutputInterface $output): void
    {
        $this->output = $output;
        if ($output) {
            $outputStyle = new OutputFormatterStyle('#008fff', '');
            $output->getFormatter()->setStyle('info', $outputStyle);
            $outputStyle = new OutputFormatterStyle('green', '');
            $output->getFormatter()->setStyle('success', $outputStyle);
        }
    }

    protected function logSuccess(string $message): void
    {
        $this->log($message, 'success');
    }

    protected function logInfo(string $message): void
    {
        $this->log($message, 'info');
    }

    protected function logWarning(string $message): void
    {
        $this->log($message, 'comment');
    }

    protected function logError(string $message): void
    {
        $this->log($message, 'error');
    }

    protected function log(string $message, string $type): void
    {
        if (isset($this->output)) {
            $this->output->writeln(
                sprintf(
                    '<%s>[%s] [%s] %s</%s>',
                    $type,
                    $this->getNow(),
                    static::class,
                    $message,
                    $type
                )
            );
        }
    }

    protected function getNow(): string
    {
        return (new \DateTime('now'))->format('Y-m-d H:i:s');
    }
}
