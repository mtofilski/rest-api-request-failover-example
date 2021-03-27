<?php

declare(strict_types=1);

namespace App\Client\Cli;

use App\Client\ActionService;
use App\Client\RetryService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TriggerActionCommand extends Command
{
    private ActionService $actionService;
    protected static $defaultName = 'action';
    private RetryService $retryService;

    public function __construct(ActionService $actionService, RetryService $retryService)
    {
        parent::__construct();
        $this->actionService = $actionService;
        $this->retryService = $retryService;
    }

    protected function configure(): void
    {
        $this->addArgument('actionType', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            for ($i = 0; $i < 100; $i++) {
                $output->writeln(
                    'Response code is: ' .
                    $this->actionService->makeSomeAction($input->getArgument('actionType'))
                );
            }
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
        }

        $output->writeln("Requests to retry: " . count($this->retryService->toRetry()));

        return self::SUCCESS;
    }
}
