<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Command;

use Odiseo\SyliusBlogPlugin\Media\MediaPruner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'odiseo:blog:media:prune', description: 'Deletes the files uploaded from the editor that no content uses.')]
final class PruneMediaCommand extends Command
{
    public function __construct(
        private readonly MediaPruner $pruner,
        private readonly int $pruneAfterDays,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'List the files without deleting them.')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Keep the files uploaded in the last days.', (string) $this->pruneAfterDays)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = $input->getOption('days');
        if (!is_string($days) || !ctype_digit($days)) {
            $io->error('--days must be a whole number of days.');

            return Command::INVALID;
        }

        $dryRun = true === $input->getOption('dry-run');
        $paths = $this->pruner->prune(new \DateTimeImmutable(sprintf('-%d days', $days)), $dryRun);

        $io->listing($paths);
        $io->success(sprintf($dryRun ? '%d unused files would be deleted.' : '%d unused files deleted.', count($paths)));

        return Command::SUCCESS;
    }
}
