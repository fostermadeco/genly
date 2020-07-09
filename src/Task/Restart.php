<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use FosterMade\Genly\Process\Process;

class Restart extends AbstractTask
{
    public function __invoke(AbstractCommand $context, ?string $service = null)
    {
        $command = $this->getCommand($context, [$service]);
        $context->output->writeln("<comment>Running `".join(' ', $command)."` in {$context->cwd}</comment>");
        Process::create($command, $context->cwd)->mustRun();
    }
}
