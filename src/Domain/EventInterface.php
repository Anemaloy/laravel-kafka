<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Domain;

interface EventInterface
{
    public function getUuid(): string;

    public function getName(): string;

    public function getVersion(): string;

    public function getTime(): \DateTimeImmutable;

    /**
     * @return mixed
     */
    public function getPayload(): mixed;
}
