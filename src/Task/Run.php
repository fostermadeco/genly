<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use FosterMade\Genly\Process\Process;

class Run extends AbstractTask
{
    public function __invoke(AbstractCommand $context, array $command, ?float $timeout = null)
    {
        $command = array_merge($context->composeCommand, [$this->getName(), '--rm'], $command);
        $context->output->writeln("<comment>Running `".join(' ', $command)."` in {$context->cwd}</comment>");
        $process = Process::create($command, $context->cwd);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->mustRun();
    }
}
