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
    name: 'cloudflare:view',
    description: 'Print currently cached Cloudflare IP ranges',
)]
class CloudflareViewCommand extends Command
{
    public function __construct(
        private readonly CloudflareIpRepository $repository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $ips = $this->repository->getIps();

        if (empty($ips)) {
            $io->warning('No Cloudflare IP ranges found in cache.');
            return Command::SUCCESS;
        }

        $io->section('Cached Cloudflare IP Ranges');
        $io->listing($ips);

        return Command::SUCCESS;
    }
}
