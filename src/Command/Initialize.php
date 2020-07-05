<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Initialize extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('initialize')
            ->setAliases(['init'])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->setOption('common', true);

        parent::execute($input, $output);

        (new Task\CreateNetwork())($this);
        (new Task\CreateCertificate())($this, $this->config->getVirtualHostForService('mailhog'));
        (new Task\Up())($this);

        foreach ($this->config->getServices() as $service => $config) {
            if ('always' === ($config['restart'] ?? null)) {
                (new Task\Start())($this, $service);
            }
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        (new Task\CreateDomainResolver())($this);
    }
}
