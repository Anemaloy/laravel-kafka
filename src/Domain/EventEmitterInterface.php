<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Domain;

interface EventEmitterInterface
{
    public function emit(EventInterface $event): void;
}
