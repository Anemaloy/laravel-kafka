<?php

namespace Anemaloy\KafkaLocator\Commands\Changelog;

use Anemaloy\KafkaLocator\Infrastructure\Schema\DirectoryLoader;
use Anemaloy\KafkaLocator\Infrastructure\Schema\EventInfo\EventInfo;
use Anemaloy\KafkaLocator\Infrastructure\Schema\EventInfo\EventInfoReference;

class ChangeLogService
{
    private EventInfoReference $eventInfoReference;

    private string $lockFilePath;

    private const CHANGE_TYPE_NEW = 'new';
    private const CHANGE_TYPE_CHANGED = 'changed';

    public function __construct(
        string $lockFilePath
    ) {
        $this->eventInfoReference = new EventInfoReference(DirectoryLoader::getEventDirectory());
        $this->lockFilePath = $lockFilePath;
    }

    public function generateChangeLogFileContent(string $newVersion): GenerationResult
    {
        $diff = $this->extractInfoDiff();

        $content = $newVersion . ' / ' . (new \DateTime())->format('Y-m-d');
        $content .= PHP_EOL;
        $content .= '===================';
        $content .= PHP_EOL;
        $content .= PHP_EOL;

        foreach ($diff as $eventName => $versions) {
            foreach ($versions as $eventData) {
                if (!$eventData['isReference']) {
                    $prefix = self::CHANGE_TYPE_NEW === $eventData['changeType'] ? 'Добавлено событие' : 'Изменено событие';
                } else {
                    $prefix = self::CHANGE_TYPE_NEW === $eventData['changeType'] ? 'Добавлена новая версия справочника' : 'Изменена версия справочника';
                }
                $content .= '    * ' . $prefix . ' ' . $this->getEventRegionTitle($eventName) . ' "' . $eventData['title'] . '" ' . $eventData['version'] . ' `' . $eventName . '`';
                $content .= PHP_EOL;
            }
        }

        $diffCount = count($diff);
        if (0 === $diffCount) {
            $content .= '    * (добавьте сюда информацию о том что поменялось)';
        }

        return new GenerationResult($content, $diffCount);
    }

    private function getEventRegionTitle(string $eventName): string
    {
        $regions = [
            'lk' => 'ЛК',
            'pa' => 'ЛК',
            'amo' => 'AMO',
        ];
        foreach ($regions as $region => $title) {
            if (false !== strpos($eventName, $region)) {
                return $title;
            }
        }

        return '';
    }

    public function updateLockFile(string $version): void
    {
        file_put_contents($this->lockFilePath, json_encode(new LockFileData($version, $this->extractInfo(false)), JSON_PRETTY_PRINT));
    }

    public function parseVersion(string $versionString): int
    {
        return (int) str_replace('.', '', $versionString);
    }

    public function getLockFileContent(): LockFileData
    {
        $content = json_decode(file_get_contents($this->lockFilePath), true, 512, JSON_THROW_ON_ERROR);

        return new LockFileData($content['version'], $content['data']);
    }

    private function extractInfoDiff(): array
    {
        $oldData = $this->getLockFileContent()->data;
        $newData = $this->extractInfo();

        ksort($oldData);
        ksort($newData);

        $diff = [];

        foreach ($newData as $eventName => $newVersions) {
            $oldVersions = $oldData[$eventName] ?? [];
            $eventDiff = [];
            foreach ($newVersions as $versionKey => $eventData) {
                if (isset($oldVersions[$versionKey])) {
                    continue;
                }
                $eventData['changeType'] = self::CHANGE_TYPE_NEW;
                foreach ($oldVersions as $oldEventData) {
                    if ($oldEventData['version'] === $eventData['version']) {
                        $eventData['changeType'] = self::CHANGE_TYPE_CHANGED;
                    }
                }
                $eventDiff[$versionKey] = $eventData;
            }

            if (0 === count($eventDiff)) {
                continue;
            }
            $diff[$eventName] = $eventDiff;
        }

        return $diff;
    }

    private function extractInfo(bool $withTitle = true): array
    {
        $info = $this->eventInfoReference->getEventsInfo();
        $result = [];
        foreach ($info as $eventName => $versions) {
            list(, $type) = explode('/', $eventName);

            if (!isset($result[$eventName])) {
                $result[$eventName] = [];
            }
            foreach ($versions as $versionKey => $versionInfo) {
                /** @var EventInfo $versionInfo */
                $hash = md5(json_encode($versionInfo->getEventData()));
                $row = ['hash' => $hash, 'version' => $versionKey, 'isReference' => in_array($type, ['refs', 'phRefs'])];
                if ($withTitle) {
                    $row['title'] = $versionInfo->getEventData()['title'];
                }
                $result[$eventName][$versionKey . '_' . $hash] = $row;
            }
        }

        return $result;
    }
}
