<?php

namespace Yng\Framework\Console\Commands;

use Yng\Console\Command;

class Vendor extends Command
{
    protected $name = 'vendor:publish';

    protected $description = 'Publish publishable packages';

    public function handle()
    {
        $installed = json_decode(file_get_contents(getcwd() . '/vendor/composer/installed.json'), true);
        $installed = $installed['packages'] ?? $installed;
        $path      = getcwd();
        foreach ($installed as $package) {
            if (isset($package['extra']['max']['config']) && is_array($config = $package['extra']['max']['config'])) {
                foreach ($config as $dir => $file) {
                    $configFile = "{$path}/config/" . basename($file);
                    if (!file_exists($configFile)) {
                        if (@copy("{$path}/vendor/max/{$dir}/{$file}", $configFile)) {
                            $this->output->info("Generate config file successfully: {$configFile}");
                        } else {
                            $this->output->error("Generate config file failed: {$configFile}");
                        }
                    }
                }
            }
        }
    }

}
