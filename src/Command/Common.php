<?php

namespace FosterMade\Genly\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class Common extends Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('common');
        $this->ignoreValidationErrors();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AbstractCommand $command */
        $command = $this->getApplication()->find($_SERVER['argv'][2]);

        if (false === $command->supportsCommon()) {
            throw new \Exception(sprintf("The '%s' command cannot be run for common containers.", $_SERVER['argv'][2]));
        }

        $args = array_slice($_SERVER['argv'], 2);
        array_splice($args, 1, 0, ['--common']);

        $in = new StringInput(join(' ', $args));

        return $command->run($in, $output);
    }
}
