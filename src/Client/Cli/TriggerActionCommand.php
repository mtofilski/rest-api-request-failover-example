<?php

declare(strict_types=1);

namespace App\Client\Cli;

use App\Client\ActionService;
use App\Client\RetryService;
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

    protected function configure()
    {
        $this->addArgument('actionType', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        for ($i = 0; $i < 100; $i++) {
            $output->writeln('Response code is: ' .
                $this->actionService->makeSomeAction($input->getArgument('actionType'))
            );
        }
        foreach($this->retryService->toRetry() as $request) {
            $output->writeln('Request to retry: ' . $request->getUri());
            $output->writeln('Response code is: ' . $this->retryService->retry($request));
        }

        $output->writeln('Looks like its the end.');

        return self::SUCCESS;
    }
}
