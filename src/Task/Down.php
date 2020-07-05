<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use FosterMade\Genly\Process\Process;

class Down extends AbstractTask
{
    public function __invoke(AbstractCommand $context)
    {
        $command = $this->getCommand($context);
        $context->output->writeln("<comment>Running `".join(' ', $command)."` in {$context->cwd}</comment>");
        Process::create($command, $context->cwd)->mustRun();
        $context->output->writeln('<info>Containers removed. ✔</info>');
    }
}
