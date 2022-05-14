<?php

namespace Yng\Framework\Providers;

use Yng\Di\AnnotationManager;
use Yng\Di\ReflectionManager;
use Yng\Event\Annotations\Listen;
use Yng\Event\ListenerProvider;
use Yng\Framework\Providers\AbstractProvider;
use Yng\Framework\Di\Traits\Discover;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @class   ListenerServiceProvider
 * @author  Yng
 * @date    2022/04/30
 * @time    20:59
 * @package Yng\Framework\Providers
 */
class ListenerServiceProvider extends AbstractProvider
{
    use Discover;

    /**
     * @var string
     */
    protected string $annotationBasedir = 'app/Listeners';
    /**
     * @var array
     */
    protected array $listeners = [];

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function register()
    {
        $this->app->alias('Psr\EventDispatcher\ListenerProviderInterface', '\Yng\Event\ListenerProvider');
        $this->app->alias('Psr\EventDispatcher\EventDispatcherInterface', '\Yng\Event\EventDispatcher');
        $this->app->set(ListenerProviderInterface::class, new ListenerProvider(...array_map(function($listener) {
            return $this->app->make($listener);
        }, $this->listeners)));
        if ($this->app->isPHP8()) {
            $this->discover(base_path($this->annotationBasedir), $this->annotationBasedir, $this->addListener());
        }
    }

    /**
     * @return mixed|void
     */
    public function boot()
    {
    }

    /**
     * @return \Closure
     */
    protected function addListener()
    {
        return function(string $classname) {
            $class = ReflectionManager::reflectClass($classname);
            if (AnnotationManager::readAnnotation($class, Listen::class)) {
                $this->app->get(ListenerProviderInterface::class)->listen($this->app->make($class->getName()));
            }
        };
    }
}
