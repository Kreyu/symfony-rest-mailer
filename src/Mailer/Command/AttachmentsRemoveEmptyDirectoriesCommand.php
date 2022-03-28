<?php

declare(strict_types=1);

namespace App\Mailer\Command;

use App\Mailer\Util\AttachmentFileManager;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class AttachmentsRemoveEmptyDirectoriesCommand extends Command
{
    public function __construct(private AttachmentFileManager $attachmentProvider)
    {
        parent::__construct('mailer:attachments:remove-empty-directories');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Usuwa z dysku puste katalogi, znajdujące się w folderze z załącznikami.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Potwierdza chęć usunięcia katalogów');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = Finder::create()
            ->in($this->attachmentProvider->getStorageDirectory())
            ->directories()
            ->sortByName()
            ->reverseSorting();

        foreach ($finder as $directory) {
            if (!$this->isDirectoryEmpty($directory)) {
                continue;
            }

            $output->writeln(sprintf('<comment>Katalog jest pusty - %s</comment>', $directory->getPathname()));

            if ($input->getOption('force')) {
                $filesystem = new Filesystem();
                $filesystem->remove($directory->getPathname());
            }
        }

        return self::SUCCESS;
    }

    private function isDirectoryEmpty(SplFileInfo $directory): bool
    {
        if (!$directory->isDir()) {
            return false;
        }

        return 0 === Finder::create()
            ->in($directory->getPathname())
            ->files()->count();
    }
}
