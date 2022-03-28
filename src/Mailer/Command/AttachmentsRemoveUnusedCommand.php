<?php

declare(strict_types=1);

namespace App\Mailer\Command;

use App\Mailer\Entity\Attachment;
use App\Mailer\Repository\AttachmentRepository;
use App\Mailer\Util\AttachmentFileManager;
use Doctrine\ORM\EntityManagerInterface;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Uid\Uuid;

class AttachmentsRemoveUnusedCommand extends Command
{
    private bool $forceRemoval = false;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private AttachmentFileManager  $attachmentProvider,
    ) {
        parent::__construct('mailer:attachments:remove-unused');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Usuwa z dysku pliki załączników, które nie mają powiązanego rekordu w bazie.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Potwierdza chęć usunięcia plików');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->forceRemoval = $input->getOption('force');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var AttachmentRepository $attachmentRepository */
        $attachmentRepository = $this->entityManager->getRepository(Attachment::class);

        $finder = Finder::create()
            ->in($this->attachmentProvider->getStorageDirectory())
            ->files();

        foreach ($finder as $file) {
            $basename = $file->getBasename();

            if (!Uuid::isValid($basename)) {
                $output->writeln(sprintf('<comment>Nazwa pliku nie jest prawidłowym UUID - %s</comment>', $file->getPathname()));

                $this->remove($file);

                continue;
            }

            $attachment = $attachmentRepository->findOneByUuid(Uuid::fromString($basename));

            if (null === $attachment) {
                $output->writeln(sprintf('<comment>Nie znaleziono w bazie załącznika o UUID równym nazwie pliku - %s</comment>', $file->getPathname()));

                $this->remove($file);
            }
        }

        return self::SUCCESS;
    }

    private function remove(SplFileInfo $file): void
    {
        if (!$this->forceRemoval) {
            return;
        }

        $filesystem = new Filesystem();
        $filesystem->remove($file->getPathname());
    }
}
