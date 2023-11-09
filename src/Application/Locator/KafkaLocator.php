<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Application\Locator;

use Anemaloy\KafkaLocator\Application\Builder;

final class KafkaLocator extends AbstractKafkaLocator
{
    /**
     * {@inheritdoc}
     */
    protected function prepareBuilderForHandlers(): Builder
    {
        return new Builder();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareBuilderForEvents(): Builder
    {
        return new Builder();
    }
}
