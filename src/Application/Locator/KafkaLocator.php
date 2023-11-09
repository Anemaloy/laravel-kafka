<?php

declare(strict_types=1);

namespace Temo\KafkaLocator\Application\Locator;

use Temo\KafkaLocator\Application\Builder;

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
