<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use FosterMade\Genly\Process\Process;

class CreateNetwork
{
    const NETWORK_NAME = 'genly';
    const CHECK = ['docker', 'network', 'ls', '--filter', 'name=^'.self::NETWORK_NAME.'$', '--format="{{ .Name }}"'];
    const CREATE = ['docker', 'network', 'create', self::NETWORK_NAME];

    public function __invoke(AbstractCommand $context)
    {
        $process = Process::create(self::CHECK, false, false)->mustRun();

        $context->output->writeln('<info>Creating "'.self::NETWORK_NAME.'" network if it does not exist.</info>');

        if (json_encode(self::NETWORK_NAME) === trim($process->getOutput())) {
            $context->output->writeln('<comment>"'.self::NETWORK_NAME.'" network exists . . . skipping task.</comment>');

            return;
        }

        $process = Process::create(self::CREATE)->mustRun();

        if (!$process->isSuccessful()) {
            $context->output->writeln('<error>"'.self::NETWORK_NAME.'" network could not be created.</error>');

            return;
        }

        $context->output->writeln('<comment>"'.self::NETWORK_NAME.'" network was successfully created!.</comment>');
    }
}
