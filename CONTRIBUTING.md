# Правила разработки

Для внесения изменений в код библиотеки и схемы событий помимо самих изменений необходимо также указать номер версии 
в файле [VERSION](./VERSION) и заполнить CHANGELOG.md (см. ниже про автогенерацию CHANGELOG.md).

Версионирование осуществляется согласно [SemVer](https://semver.org/lang/ru/). При выпуске новых версий схем событий
должна быть увеличена минорная версия. При исправлении существующих версий схем событий, которые ещё не вышли в релиз,
увеличивается патч-версия.

Код должен быть оформлен согласно принятым правилам code style:

```bash
make ecs-check # проверка оформления кода
make ecs-fix # исправление ошибок оформления
```

## Автогенерация CHANGELOG.md

    php bin/console changelog:generate {newVersion} --write

(будут обновлены CHANGELOG.md и VERSION а так же будет обновлен файл `version.lock`)

    php bin/console changelog:generate {newVersion} --dump
(предпросмотр изменений без перезаписи файлов)

    php bin/console changelog:generate --init
(создание файла `version.lock` на основе текущего состояния папки events, команда нужна только для первоначальной инициализации или починки файла)

    php bin/console changelog:generate --write --force
(принудительно обновить версию даже если нет изменений в событиях)


Команда умеет генерить ченжлог на основании изменений в структуре папки events (отобразит добавление новых версий событий и факт изменения контента существующих)

Что конкретно поменялось в новой версии события можно уже дописать руками если есть такая необходимость.

Для того чтобы команда работала корректно, в корне репозитория живет файл `version.lock` который эта команда обновляет автоматом вместе с генерацией ченжлога, для поддержания его в актуальном состоянии (по аналогии с композером и его _composer.lock_)

Даже если нет желания пользоваться автогенерацией - в конце работы над новой версией библиотеки нужно выполнить `changelog:generate --init` чтобы актуализировать этот файл.

Либо также можно выполнять эту команду при старте работы над новой версией библиотеки дабы по окончанию работ автогенерация сгенерила CHANGELOG именно по текущей задаче


Возможные улучшения на будущее, которые можно было бы добавить:
* добавить выполнение `changelog:generate --init`  прям в **CI** чтобы файл всегда автоматом поддерживался в актуальном виде
* реализовать анализ диффа между старой и новой версией и выводить подробности по поменявшимся полям
