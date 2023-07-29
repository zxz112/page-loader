<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
/**
 * @psalm-suppress UnusedClass
 */
class PageLoadCommand extends Command
{
    protected static $defaultDescription = 'Creates a new user.';

    protected function configure(): void
    {
        $this->setHelp('This command allows you to download web page and save assets');
        $this->addArgument('url', InputArgument::REQUIRED);
        $this->addArgument('path', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var string $url
         */
        $url = $input->getArgument('url');
        /**
         * @var string $path
         */
        $path = $input->getArgument('path');
        try {
            $client = new \App\Client\GuzzleClient();
            $logger = new ConsoleLogger($output);
            $parser = new \App\PageLoader(
                $url,
                $client,
                $logger
            );

            $pathLoadedPage = $parser->load($path);
            $output->writeln("<info>" . $pathLoadedPage . "</info>");
        } catch (\Exception $exception) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}