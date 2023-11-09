<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Domain;

interface EventEmitterInterface
{
    public function emit(EventInterface $event): void;
}
