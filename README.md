# kvarta-shared/event-collaboration

Библиотека предназначена для передачи событий в формате JSON через Apache Kafka. 

Библиотека выполняет сериализацию и десериалзиацию данных событий, публикацию и чтение событий из топиков Kafka, 
а также валидацию данных событий на основе JSON-схемы.

# Установка

Для установки `kvarta-shared/event-collaboration` необходимо подключить composer-репозиторий и затем установить пакет
стандартным способом:

```console
$ composer config repositories.git.structure.pik-broker.ru/231 '{"type": "composer", "url": "https://git.structure.pik-broker.ru/api/v4/group/231/-/packages/composer/packages.json"}'
$ composer require kvarta-shared/event-collaboration
```

# Создание каталога событий

Для хранения каталога событий рекомендуется создать отдельный пакет composer с зависимостью от `kvarta-shared/event-collaboration`.

Каталог событий должен содержать в себя одну или несколько директорий со схемами событий в формате JSON,
каждая директория соответствует префиксу URI схем в этой директории.

Также стоит включить в каталог событий phpunit тест существующих схем:

```php
<?php

namespace PikBroker\MySchemaCatalog\Tests\Functional;

use Anemaloy\KafkaLocator\Tests\Functional\AbstractCheckJsonFilesTest;

class CheckJsonFilesTest extends AbstractCheckJsonFilesTest
{

    protected function getEventDirectories(): iterable
    {
        yield 'https://foo.domain.org/' => __DIR__ . '/../../Resources/events/foo.domain.org';
        yield 'https://bar.domain.org/' => __DIR__ . '/../../Resources/events/bar.domain.org';
    }
}
 
```

# Документация

- Правила наименований событий и размещения JSON-схем описаны в [Resources/docs/events.md](./Resources/docs/events.md).
- Информация о работе с Apache Kafka находится в [Resources/docs/kafka.md](./Resources/docs/kafka.md).
- Примеры использования библиотеки доступны в [Resources/docs/examples](./Resources/docs/examples).

# Правила разработки

Правила разработки описаны в [CONTRIBUTING.md](./CONTRIBUTING.md).
