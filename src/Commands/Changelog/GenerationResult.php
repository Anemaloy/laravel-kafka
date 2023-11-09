<?php

namespace Anemaloy\KafkaLocator\Commands\Changelog;

class GenerationResult
{
    public string $content = '';
    public int $diffCount;

    public function __construct(string $content, int $diffCount)
    {
        $this->content = $content;
        $this->diffCount = $diffCount;
    }
}
