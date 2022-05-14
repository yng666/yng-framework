<?php
declare(strict_types=1);

namespace Yng\Framework\Console\Commands;

use Yng\Console\{Command, Exception\InvalidOptionException};
use Yng\Routing\RouteCollector;

class Route extends Command
{

    /**
     * @var string
     */
    protected $name = 'route';

    /**
     * @var string
     */
    protected $description = 'Manage your routers';

    /**
     * 缓存文件
     *
     * @var string
     */
    protected $cacheFile;

    /**
     *
     */
    protected const SEPARATOR = "+---------------------------+------------------------------------------------------------+---------------------------------------------+----------------+\n";

    /**
     * 初始化配置
     */
    public function __construct()
    {
        $this->cacheFile = env('cache_path') . 'route/route.php';
    }

    /**
     * @throws InvalidOptionException
     */
    public function handle()
    {
        $input = $this->input;
        if (!$input->hasParameters() || $input->hasArgument('--help') || $input->hasArgument('-H')) {
            echo $this->help();
            exit;
        }
        if ($input->hasArgument('--list') || $input->hasArgument('-L')) {
            return $this->list();
        }
        if ($input->hasArgument('--cache')) {
            if ($input->hasArgument('-d')) {
                return $this->deleteCache();
            }
            return $this->createCache();
        }
        throw new InvalidOptionException("Use `php max route --help` or `php max route -H` to look up for usable options.");
    }

    /**
     * @return string
     */
    public function help()
    {
        $name = str_pad("php max {$this->name} [option]", 33, ' ', STR_PAD_RIGHT);
        return <<<EOT
\033[33m{$name}\033[0m           {$this->getDescription()}
Options:
          -L,        --list                 List all routers           
          -H,        --help                 Show helper             
          --cache [-d]                      Create a cache file for the route  
                                            Use -d to delete cached route files

EOT;
    }

    /**
     * @return \Yng\Utils\Collection
     */
    protected function getRoutes()
    {
        RouteCollector::compile();
        $registedRoutes = RouteCollector::all();
        $routes         = [];
        foreach ($registedRoutes as $registedRoute) {
            foreach ($registedRoute as $method => $route) {
                if (!in_array($route, $routes)) {
                    $routes[] = $route;
                }
            }
        }
        return collect($routes)->unique()->sortBy(function($item) {
            return $item->getUri();
        });
    }

    /**
     * 路由列表输出
     */
    public function list()
    {
        echo self::SEPARATOR . "|" . $this->format(' METHODS', 26) . " |" . $this->format('URI', 60) . "|" . $this->format('DESTINATION', 45) . "|  " . $this->format('NAME', 14) . "|\n" . self::SEPARATOR;
        foreach ($this->getRoutes() as $route) {
            /** @var \Yng\Routing\Route $route */
            $action = $route->getAction();
            if (is_array($action)) {
                $action = implode('@', $action);
            } else if ($action instanceof \Closure) {
                $action = '\Closure';
            }
            echo '|' . $this->format(strtoupper(implode('|', $route->getMethods())), 27) . '|' . $this->format($route->getUri(), 60) . '|' . $this->format($action, 45) . '| ' . $this->format($route->getName() ?? '', 15) . "|\n";
        }

        exit(self::SEPARATOR);
    }

    /**
     * 生成路由缓存
     * 现在还存在bug，list重复
     */
    public function createCache()
    {
        file_exists(dirname($this->cacheFile)) || mkdir(dirname($this->cacheFile), 0755, true);
        file_exists($this->cacheFile) && unlink($this->cacheFile);
        file_put_contents($this->cacheFile, \Opis\Closure\serialize($this->app->get(RouteCollector::class)));
        return $this->output->info('缓存成功！');
    }

    /**
     * 删除路由缓存
     */
    public function deleteCache()
    {
        if (!file_exists($this->cacheFile)) {
            return $this->output->error('没有缓存文件！');
        }
        unlink($this->cacheFile);
        return $this->output->info('缓存清除成功！');
    }


    /**
     * 格式化文本，给两端添加空格
     *
     * @param $string
     * @param $length
     *
     * @return string
     */
    private function format($string, $length)
    {
        return str_pad($string, $length, ' ', STR_PAD_BOTH);
    }

}
