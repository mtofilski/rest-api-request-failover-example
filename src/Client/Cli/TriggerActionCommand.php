<?php

declare(strict_types=1);

namespace App\Client\Cli;

use App\Client\ActionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TriggerActionCommand extends Command
{
    private ActionService $actionService;
    protected static $defaultName = 'action';

    public function __construct(ActionService $actionService)
    {
        parent::__construct();
        $this->actionService = $actionService;
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


        $output->writeln('Looks like its the end.');

        return self::SUCCESS;
    }
}
