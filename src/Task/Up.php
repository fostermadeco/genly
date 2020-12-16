<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use FosterMade\Genly\Process\Process;

class Up extends AbstractTask
{
    public function __invoke(AbstractCommand $context)
    {
        $command = $this->getCommand($context, ['--no-start']);
        $context->output->writeln("<comment>Running `".join(' ', $command)."` in {$context->cwd}</comment>");
        (Process::create($command, $context->cwd))->setTimeout(null)->mustRun();
        $context->output->writeln('<info>Containers created. ✔</info>');
    }
}
