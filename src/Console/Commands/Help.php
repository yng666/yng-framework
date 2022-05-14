<?php

namespace Yng\Framework\Console\Commands;

use Yng\Console\{Command, Console, Output};

class Help extends Command
{

    protected $name = 'help';

    protected $description = 'Show commands list';

    public function handle()
    {
        $commands = $this->app->console->getAllCommands();
        $this->writeLine('Usage:', Output::COLOR_AZURE);
        foreach ($commands as $name => $command) {
            $command = $this->app->make($command, [], true);
            $helper  = $command->help();
            if (empty($helper)) {
                $name = str_pad($name, 36, ' ', STR_PAD_RIGHT);
                $this->output->warning("php max {$name}\033[0m{$command->getDescription()}");
            }
            $this->output->write($command->help() ?: '');
        }
    }

}
