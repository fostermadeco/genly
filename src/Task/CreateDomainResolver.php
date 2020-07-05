<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;
use FosterMade\Genly\Process\Process;
use Symfony\Component\Console\Question\Question;

class CreateDomainResolver
{
    const PATH = '/etc/resolver/test';
    const LINE = 'nameserver 127.0.0.1';
    const COMMAND = 'sudo touch '.self::PATH.' && echo "'.self::LINE.'\n" | sudo tee '.self::PATH;
    const RESTART = ['sudo', 'shutdown', '-r', 'now'];

    public function __invoke(AbstractCommand $context)
    {
        $context->output->writeln('<info>Creating .test domain resolver if it does not exist.</info>');

        $content = '';

        if (file_exists(self::PATH) && self::LINE === ($content = trim(file_get_contents(self::PATH)))) {
            $context->output->writeln('<comment>The .test domain resolver already exists . . . skipping task.</comment>');

            return;
        }

        $questionHelper = $context->getHelper('question');

        if (!empty($content)) {
            $context->output->writeln('<comment>The .test domain resolver file exists, but its contents are not compatible with genly.</comment>');
            $context->output->writeln('<comment>Its contents are — </comment>' . PHP_EOL);
            $context->output->writeln(rtrim($content) . PHP_EOL);

            $question = new Question('<question>Do you want to replace the contents of the file? [Y/n]</question> ', 'Y');
            $response = $questionHelper->ask($context->input, $context->output, $question);

            if (0 !== strcasecmp('y', $response)) {
                throw new \Exception('Initialization failed. The existing .test domain resolver is incompatible with genly.');
            }
        }

        $context->output->writeln('<comment>Attempting to update "'.self::PATH.'".</comment>');
        $context->output->writeln('<comment>You may be asked for your password to complete this action.</comment>');

        Process::fromShellCommandline(self::COMMAND)->mustRun();

        $context->output->writeln('<info>The .test domain resolver was successfully created! ✔</info>');
        $context->output->writeln('<info>The host machine must now be restarted for the resolver to take effect.</info>');
        $context->output->writeln('<info>The common genly containers should start automatically once Docker is launched.</info>');

        $question = new Question('<question>Do you want to restart your host machine now? [Y/n]</question> ', 'Y');
        $response = $questionHelper->ask($context->input, $context->output, $question);

        if (0 !== strcasecmp('y', $response)) {
            $context->output->writeln('<comment>You must manually restart your machine.</comment>');
        } else {
            $context->output->writeln('<comment>Your machine will be restarted.</comment>');
            Process::create(self::RESTART)->mustRun();
        }
    }
}
