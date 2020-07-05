<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Up extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('up');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        (new Task\Up())($this);
    }
}
