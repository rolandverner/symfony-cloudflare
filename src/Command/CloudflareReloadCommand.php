<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Command;

use Cloudflare\TrustedProxies\Service\CloudflareIpRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'cloudflare:reload',
    description: 'Reload Cloudflare IP ranges into cache',
)]
class CloudflareReloadCommand extends Command
{
    public function __construct(
        private readonly CloudflareIpRepository $repository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->comment('Fetching Cloudflare IP ranges...');
            $this->repository->refresh();
            $io->success('Cloudflare IP ranges have been reloaded into cache.');
        } catch (\Throwable $e) {
            $io->error(sprintf('Failed to reload Cloudflare IPs: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
