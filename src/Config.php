<?php

namespace FosterMade\Genly;

class Config
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getServices(): array
    {
        return $this->config['services'] ?? [];
    }

    public function getVirtualHostForService(string $service): string
    {
        return $this->config['services'][$service]['environment']['VIRTUAL_HOST'] ?? '';
    }
}
