<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Yarn extends AbstractCommand
{
    public function supportsCommon(): bool
    {
        return false;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('yarn');
        $this->ignoreValidationErrors();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $argv = $_SERVER['argv'];
        array_splice($argv, 0, 1, ['node']);
        (new Task\Run())($this, $argv);
    }
}
