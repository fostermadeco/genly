<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use FosterMade\Genly\Process\Process;

class Start extends AbstractTask
{
    public function __invoke(AbstractCommand $context, string $service)
    {
        $command = array_merge($context->composeCommand, [$this->getName(), $service]);
        $context->output->writeln("<comment>Running `".join(' ', $command)."` in {$context->cwd}</comment>");
        Process::create($command, true, true, $context->cwd)->mustRun();
        $context->output->writeln("<info>Service '{$service}' started. ✔</info>");
    }
}
