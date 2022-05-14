<?php
declare (strict_types=1);

namespace Yng\Framework;

use App\Console\Kernel;
use Yng\Config\Repository;
use Yng\Console\Application;
use Yng\Contracts\ErrorHandlerInterface;
use Yng\Di\Container;
use Yng\Env\{Env,Loader\IniFileLoader};
use Yng\Framework\Contracts\ServiceProviderInterface;


/**
 * 框架引导文件
 * @property Kernel           $console        命令行
 * @property \App\Http\Kernel $http           命令行
 * @class   App
 * @author  Yng
 * @date    2022/04/18
 * @time    23:22
 * @package Yng\Framework
 */
class App extends Container
{
    /**
     * Debug Mode
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * @var string
     */
    protected $rootPath = '..';

    /**
     * @var ServiceProviderInterface[]
     */
    protected array $serviceProviders = [];

    /**
     * 绑定的类名
     *
     * @var array|string[]
     */
    protected array $alias = [
        'config' => Repository::class,
    ];

    /**
     * App constructor.
     */
    public function __construct(string $rootPath = '..')
    {
        $this->setRootPath($rootPath);
        static::$instance = $this;
        $this->set('Yng\Framework\App', $this);
        $this->set('Yng\Framework\Container', $this);
    }

    /**
     * 初始化配置
     *
     * @return $this
     */
    protected function initializeConfiguration()
    {
        $rootPath = $this->getRootPath();
        $env      = $this->make(Env::class);
        $env->load(new IniFileLoader($rootPath . '.env'));
        $env->push([
            'ROOT_PATH'    => $rootPath,
            'APP_PATH'     => $rootPath . 'app/',
            'CONFIG_PATH'  => $rootPath . 'config/',
            'STORAGE_PATH' => $rootPath . 'storage/',
            'ROUTE_PATH'   => $rootPath . 'routes/',
            'PUBLIC_PATH'  => $rootPath . 'public/',
            'CACHE_PATH'   => $rootPath . 'storage/cache/',
        ]);
        $this->make(Repository::class)->load(glob($rootPath . 'config/*.php'));

        return $this;
    }

    /**
     * @param \Closure $handler
     */
    public function handle(\Closure $handler)
    {
        $this->initializeConfiguration();
        $config      = $this->config->get('app');
        $this->debug = (bool)$config['debug'];
        $this->alias = array_merge($config['di']['bindings'], $this->alias);
        date_default_timezone_set($config['default_timezone'] ?? 'PRC');
        $handler($this);
    }

    /**
     * @param ServiceProviderInterface ...$serviceProviders
     *
     * @return void
     */
    public function register(ServiceProviderInterface ...$serviceProviders)
    {
        $this->serviceProviders = $serviceProviders;
        foreach ($serviceProviders as $serviceProvider) {
            $serviceProvider->register();
        }
    }

    /**
     * @return void
     */
    public function boot()
    {
        foreach ($this->serviceProviders as $serviceProvider) {
            $serviceProvider->boot();
        }
    }

    /**
     * 判断是否是debug模式
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * 是不是PHP8
     *
     * @return bool
     */
    public function isPHP8(): bool
    {
        return PHP_VERSION_ID >= 80000;
    }

    /**
     * rootpath
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * 设置根路径
     *
     * @param string $path
     *
     * @return $this
     */
    public function setRootPath(string $path)
    {
        $this->rootPath = realpath($path) . DIRECTORY_SEPARATOR;

        return $this;
    }

    /**
     * 判断是否是cli
     *
     * @return bool
     */
    public function isCli()
    {
        return 'cli' === PHP_SAPI;
    }

}








// ini_set("display_errors", "On");
// error_reporting(E_ALL);

// require dirname(__DIR__).DIRECTORY_SEPARATOR.'yng'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'constant.php';
// require dirname(__DIR__).DS.'yng'.DS.'library'.DS.'loader.php';
// require dirname(__DIR__).DS.'yng'.DS.'common'.DS.'common.php';

// spl_autoload_register('yng\\library\\Loader::load');// 自动加载use函数

// Env::load();//加载框架env配置

// Route::run();//加载路由



// dd($router);

/**
 * 一、获取用户输入的控制器名和方法名
 * 1.用户输入url有两种方法
 * (1)ww.yng.com/index.php?c=控制器名/方法名
 * (2)www.yng.com/index.php/控制器名/方法名
 * 
 * 判断用户输入url的方法
 * 1.可以通过php内置的$_SERVER['QUERY_STRING']获取传参部分,利用QUERTY_STRING判断它是哪种类型的url.
 * 2.其中PATH_INFO可以得到我们的PATHINFO方式传递的值
 */

// $query_url = $_SERVER['QUERY_STRING'] ? $_GET['s'] :$_SERVER['PATH_INFO'];


// // 二、从这个字符串中获取控制器名和方法名
// if($query_url){
//     $query_url = explode('/',trim($query_url,'/'));
//     list($controller,$method) = $query_url;//分别赋值位控制器名和方法名
//     $controller = '\\app\\controller\\' .ucfirst($controller).'Controller';
//     $method = $method;
// }else{
//     $controller = '\\app\\controller\\IndexController';
//     $method = 'index';
// }


// 使用php的spl_autoload_register()函数,它会自动引入文件,它的参数是个匿名函数,未定义或引入的类，将会作为匿名函数的参数传入到该函数中

// // 使用路径自动加载类和方法,就不能和命名空间一起使用
// spl_autoload_register(function($class){
//     var_dump($class);
//     $class_path = str_replace('\\', DS, ROOT.$class).'.php';
//     if(file_exists($class_path)){
//         include($class_path);
//     }else{
//         die($class_path.'不存在');
//     }
// });

// (new $controller)->$method();

// echo $return;



