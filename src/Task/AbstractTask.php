<?php

namespace FosterMade\Genly\Task;

use FosterMade\Genly\Command\AbstractCommand;

abstract class AbstractTask
{
    public function getName()
    {
        return strtolower((new \ReflectionClass(static::class))->getShortName());
    }

    public function getCommand(AbstractCommand $context, ?array ...$options)
    {
        return array_merge($context->composeCommand, [$this->getName()], ...$options);
    }
}
