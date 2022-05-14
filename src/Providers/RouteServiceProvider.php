<?php

namespace Yng\Framework\Providers;

use Yng\Config\Repository;
use Yng\Di\AnnotationManager;
use Yng\Di\ReflectionManager;
use Yng\Framework\Providers\AbstractProvider;
use Yng\Framework\App;
use Yng\Framework\Di\Traits\Discover;
use Yng\Routing\Annotations\Controller;
use Yng\Routing\Contracts\MappingInterface;
use Yng\Routing\RouteCollector;
use Yng\Routing\Router;

class RouteServiceProvider extends AbstractProvider
{
    use Discover;

    /**
     * @var string
     */
    protected string $cachePath;

    /**
     * @var string
     */
    protected string $cacheName = 'route.php';

    /**
     * @var string|array|\ArrayAccess|mixed
     */
    protected string $annotationBaseDir;

    /**
     * @var bool|array|\ArrayAccess|mixed
     */
    protected bool $cache = false;

    /**
     * @param App $app
     *
     * @throws \Exception
     */
    final public function __construct(App $app)
    {
        parent::__construct($app);
        /* @var Repository $config */
        $config                  = $app->make(Repository::class);
        $this->cache             = $config->get('route.cache', false);
        $this->enableAnnotation  = $config->get('route.annotation.enable', false);
        $this->annotationBaseDir = $config->get('route.annotation.base_dir', 'app/Http/Controllers');
        $this->cachePath         = env('cache_path') . 'route/';
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    final public function register()
    {
        $routeCache = $this->cachePath . $this->cacheName;
        if (!\file_exists($this->cachePath)) {
            \mkdir($this->cachePath, 0755, true);
        }
        if ($this->cache && \file_exists($routeCache)) {
            RouteCollector::replace(\Opis\Closure\unserialize(\file_get_contents($routeCache)));
        } else {
            $this->map(new Router());
            if ($this->app->isPHP8() && $this->enableAnnotation) {
                $this->discover(base_path($this->annotationBaseDir), $this->annotationBaseDir, $this->registerRoute());
            }
            if ($this->cache && !file_exists($routeCache)) {
                \file_put_contents($routeCache, \Opis\Closure\serialize(RouteCollector::all()));
            }
        }
        RouteCollector::compile();
    }

    /**
     * @return mixed|void
     */
    public function boot()
    {
    }

    /**
     * @param Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
    }

    /**
     * @return \Closure
     */
    final protected function registerRoute()
    {
        return function(string $file) {
            $class      = ReflectionManager::reflectClass($file);
            $controller = AnnotationManager::readAnnotation($class, Controller::class);
            foreach ($class->getMethods() as $method) {
                $attributes = $method->getAttributes();
                foreach ($attributes as $attribute) {
                    $instance = $attribute->newInstance();
                    if ($instance instanceof MappingInterface) {
                        $instance->register($class->getName(), $method->getName());
                    }
                }
            }
        };
    }

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return RouteCollector::$router->{$method}(...$args);
    }
}
