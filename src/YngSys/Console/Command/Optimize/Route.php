<?php
namespace Yng\Console\Command\Optimize;

use Yng\Console\Command;
use Yng\Console\Input;
use Yng\Console\Input\Argument;
use Yng\Console\Output;
use Yng\Event\RouteLoaded;

class Route extends Command
{
    protected function configure()
    {
        $this->setName('optimize:route')
            ->addArgument('dir', Argument::OPTIONAL, 'dir name .')
            ->setDescription('Build app route cache.');
    }

    protected function execute(Input $input, Output $output)
    {
        $dir = $input->getArgument('dir') ?: '';

        $path = $this->app->getStoragePath() . 'framework' . DIRECTORY_SEPARATOR . ($dir ? $dir . DIRECTORY_SEPARATOR : '');

        $filename = $path . 'route.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        file_put_contents($filename, $this->buildRouteCache($dir));
        $output->writeln('<info>Succeed!</info>');
    }

    protected function buildRouteCache(string $dir = null): string
    {
        $this->app->route->clear();
        $this->app->route->lazy(false);

        // 路由检测
        $path = $this->app->getRootPath() . ($dir ? 'app' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR : '') . 'route' . DIRECTORY_SEPARATOR;

        $files = is_dir($path) ? scandir($path) : [];

        foreach ($files as $file) {
            if (str_contains($file, '.php')) {
                include $path . $file;
            }
        }

        //触发路由载入完成事件
        $this->app->event->trigger(RouteLoaded::class);
        $rules = $this->app->route->getName();

        return '<?php ' . PHP_EOL . 'return unserialize(\'' . serialize($rules) . '\');';
    }

}
