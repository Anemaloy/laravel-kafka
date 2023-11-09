<?php

namespace Temo\KafkaLocator\Commands\Changelog;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateChangelogCommand extends Command
{
    protected static $defaultName = 'changelog:generate';
    private static string $lockFilePath = __DIR__ . '/../../../version.lock';
    private static string $versionFilePath = __DIR__ . '/../../../VERSION';
    private static string $changelogFilePath = __DIR__ . '/../../../CHANGELOG.md';

    private ChangeLogService $changeLogService;

    public function __construct(string $name = null)
    {
        $this->changeLogService = new ChangeLogService(self::$lockFilePath);
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('version', InputArgument::OPTIONAL, 'В ручную указать новую версию библиотеки');
        $this->addOption('write', 'w', InputOption::VALUE_NONE, 'Обновить файлы CHANGELOG.md и VERSION (иначе - просто вывести на экран)');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Обновить версию и файлы CHANGELOG.md и VERSION не смотря на отсутствие изменений');
        $this->addOption('init', 'i', InputOption::VALUE_NONE, 'Выполнить команду с этой опцией если нужно сгенерировать version.lock с нуля');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currentVersion = trim(file_get_contents(self::$versionFilePath));

        if ($input->getOption('init')) {
            $this->changeLogService->updateLockFile($currentVersion);
            $output->writeln('version.lock создан');

            return Command::SUCCESS;
        }

        $newVersion = $input->getArgument('version');
        if (!$newVersion) {
            $newVersion = $this->incrementVersion($currentVersion);
        } else {
            if ($this->changeLogService->parseVersion($newVersion) <= $this->changeLogService->parseVersion($currentVersion)) {
                $output->writeln('Новая версия должна превосходить предыдущую');

                return Command::INVALID;
            }
        }

        $output->writeln('-------');
        $output->writeln('Версия: ' . $currentVersion . ' => ' . $newVersion);
        $output->writeln('-------');
        $output->writeln(PHP_EOL);

        $generationResult = $this->changeLogService->generateChangeLogFileContent($newVersion);

        if (0 === $generationResult->diffCount && !$input->getOption('force')) {
            $output->writeln('Изменений нет');

            return Command::SUCCESS;
        }

        if (!$input->getOption('write')) {
            $output->writeln($generationResult->content);

            return Command::SUCCESS;
        }

        if (!file_exists(self::$changelogFilePath)) {
            file_put_contents(self::$changelogFilePath, '');
        }
        $existingContent = file_get_contents(self::$changelogFilePath);
        file_put_contents(self::$changelogFilePath, $generationResult->content . PHP_EOL . $existingContent);
        file_put_contents(self::$versionFilePath, $newVersion);
        $this->changeLogService->updateLockFile($newVersion);

        $output->writeln('файлы CHANGELOG.md и VERSION обновлены');

        return Command::SUCCESS;
    }

    private function incrementVersion(string $versionString): string
    {
        list($major, $minor) = explode('.', $versionString);

        return implode('.', [$major, (int) $minor + 1, 0]);
    }
}
