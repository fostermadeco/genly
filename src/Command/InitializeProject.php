<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeProject extends AbstractCommand
{
    public function supportsCommon(): bool
    {
        return false;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('initialize:project')
            ->setAliases(['init:project'])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        (new Task\CreateCertificate())($this, $this->config->getVirtualHostForService('web'));
        (new Task\CreateCertificate())($this, $this->config->getVirtualHostForService('node'));

        (new Task\Up())($this);
        (new Task\Start())($this, 'web');

        $output->writeln('<info>Restarting nginx-proxy.</info>');

        $this->cwd = dirname(self::COMMON_DOCKER_COMPOSE_PATH);
        (new Task\Restart())($this, 'nginx-proxy');

        $output->writeln('<comment>Nginx restarted — it may up to 90 seconds before nginx serves your local projects.</comment>');
    }
}
