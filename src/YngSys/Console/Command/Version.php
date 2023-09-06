<?php
declare (strict_types = 1);

namespace Yng\Console\Command;

use Composer\InstalledVersions;
use Yng\Console\Command;
use Yng\Console\Input;
use Yng\Console\Output;

class Version extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('version')->setDescription('show YNGPHP framework version');
    }

    protected function execute(Input $input, Output $output)
    {
        $version = InstalledVersions::getPrettyVersion('yng/yng-framework');
        $output->writeln($version);
    }

}
