<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Infrastructure\Event;

use Temo\KafkaLocator\Domain\EventInterface;

interface EventValidatorInterface
{
    public function validate(EventInterface $event, int $maxErrors = 1): EventValidationResultInterface;
}
