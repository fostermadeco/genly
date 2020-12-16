<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends AbstractCommand
{
    public function supportsCommon(): bool
    {
        return false;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('run');
        $this->ignoreValidationErrors();

        $this->addOption('service', 's', InputOption::VALUE_OPTIONAL, 'The service in which to run the command.', 'web');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $argv = $_SERVER['argv'];

        array_splice($argv, 0, 2, [$input->getOption('service')]);

        (new Task\Run())($this, $argv);
    }
}
