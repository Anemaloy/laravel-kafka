<?php

namespace Anemaloy\KafkaLocator\Commands\Changelog;

class LockFileData
{
    public string $version;
    public array $data;

    public function __construct(string $version, array $data)
    {
        $this->version = $version;
        $this->data = $data;
    }
}
