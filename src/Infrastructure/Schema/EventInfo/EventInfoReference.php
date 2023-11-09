<?php

namespace Anemaloy\KafkaLocator\Infrastructure\Schema\EventInfo;

use Composer\Semver\Comparator;

class EventInfoReference
{
    private array $filesList;
    private array $eventsInfo;

    public function __construct(string $eventDirectory)
    {
        $this->filesList = $this->getJsonFilesList($eventDirectory);
        $this->initEventsInfo($eventDirectory);
    }

    public function getFilesList(): array
    {
        return $this->filesList;
    }

    private function initEventsInfo(string $eventDirectory): void
    {
        $events = [];

        foreach ($this->filesList as $file) {
            $eventRawName = str_replace([$eventDirectory . '/', '.json'], '', $file);
            $parts = explode('/', $eventRawName);
            $eventVersion = end($parts);
            $eventName = str_replace('/' . $eventVersion, '', $eventRawName);

            if (empty($events[$eventName])) {
                $events[$eventName] = [];
            }

            $eventData = json_decode(file_get_contents($file), true);
            $events[$eventName][$eventVersion] = new EventInfo($file, $eventName, $eventVersion, $eventData);
        }

        $this->eventsInfo = $events;
    }

    private function getJsonFilesList(string $eventDirectory): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($eventDirectory));

        $files = [];
        foreach ($rii as $file) {
            if (!$file->isDir() && 'json' == pathinfo($file->getPathname(), PATHINFO_EXTENSION)) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public function getEventsInfo(): array
    {
        return $this->eventsInfo;
    }

    public function hasEvent(string $eventName): bool
    {
        return array_key_exists($eventName, $this->eventsInfo);
    }

    public function hasEventVersion(string $eventName, string $eventVersion): bool
    {
        if (!$this->hasEvent($eventName)) {
            return false;
        }

        return array_key_exists($eventVersion, $this->eventsInfo[$eventName]);
    }

    public function getEventVersions(string $eventName): array
    {
        return $this->hasEvent($eventName) ? $this->eventsInfo[$eventName] : [];
    }

    public function getLastEventVersion(string $eventName): ?string
    {
        if (!$this->hasEvent($eventName)) {
            return null;
        }

        $eventVersions = array_keys($this->eventsInfo[$eventName]);
        $sortFunction = function ($version1, $version2) {
            return Comparator::greaterThan($version1, $version2) ? 1 : -1;
        };

        usort($eventVersions, $sortFunction);

        return end($eventVersions);
    }

    public function getEventInfo(string $eventName, string $eventVersion): ?EventInfo
    {
        if (!$this->hasEventVersion($eventName, $eventVersion)) {
            return null;
        }

        return $this->eventsInfo[$eventName][$eventVersion];
    }

    public function getLastEventInfo(string $eventName): ?EventInfo
    {
        $eventVersion = $this->getLastEventVersion($eventName);

        if (null === $eventVersion) {
            return null;
        }

        return $this->eventsInfo[$eventName][$eventVersion];
    }
}
