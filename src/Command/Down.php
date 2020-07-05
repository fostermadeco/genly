<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Down extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('down');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        (new Task\Down())($this);
    }
}
