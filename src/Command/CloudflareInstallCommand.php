<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'cloudflare:install',
    description: 'Installs the default configuration file for the Cloudflare Trusted Proxies bundle.'
)]
class CloudflareInstallCommand extends Command
{
    public function __construct(
        private string $projectDir
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force overwrite existing configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $configDir = $this->projectDir . '/config/packages';
        $targetFile = $configDir . '/cloudflare_proxy.yaml';
        $sourceFile = __DIR__ . '/../Resources/config/packages/cloudflare_proxy.yaml';

        if (!is_dir($configDir)) {
            $io->error(sprintf('The directory "%s" does not exist. Are you sure you are in a Symfony project?', $configDir));
            return Command::FAILURE;
        }

        if (file_exists($targetFile) && !$input->getOption('force')) {
            $io->warning('The configuration file "config/packages/cloudflare_proxy.yaml" already exists.');
            $io->note('Use --force to overwrite it.');
            return Command::SUCCESS;
        }

        if (!copy($sourceFile, $targetFile)) {
            $io->error('Failed to copy the configuration file.');
            return Command::FAILURE;
        }

        $io->success('Configuration file created: config/packages/cloudflare_proxy.yaml');

        return Command::SUCCESS;
    }
}
