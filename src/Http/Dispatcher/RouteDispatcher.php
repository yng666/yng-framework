<?php

namespace Yng\Framework\Http\Dispatcher;

use Yng\Di\AnnotationManager;
use Yng\Framework\Http\Response;
use Yng\Http\Server\RequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @class   RouteDispatcher
 * @author  Yng
 * @date    2022/04/23
 * @time    19:40
 * @package Yng\Framework\Http\Dispatcher
 */
class RouteDispatcher implements RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 解析路由目标
     *
     * @param $action
     *
     * @return mixed|string[]
     */
    protected function parseAction($action)
    {
        if (is_string($action) && strpos($action, '@')) {
            return explode('@', $action, 2);
        }

        return $action;
    }

    /**
     * @param array  $middlewares
     * @param string $method
     *
     * @return array
     */
    protected function parseMiddleware(array $middlewares, string $method): array
    {
        $middleware = [];
        //TODO 优先级
        foreach ($middlewares as $class => $rule) {
            if ($rule instanceof \Closure || is_object($rule)) {
                $middleware[] = $rule;
            } else if (
                ([] !== $rule['only'] && in_array($method, $rule['only']))
                || ([] === $rule['only'] && !in_array($method, $rule['except']))
            ) {
                $middleware[] = $class;
            }
        }

        return $middleware;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Yng\Framework\Exceptions\HttpException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $action = $this->parseAction($request->route->getAction());
        if ($action instanceof \Closure) {
            return Response::make($this->container->invokeFunc($action, array_filter($request->route->getParameters(), function($value) {
                return !is_null($value);
            })));
        }
        if (is_array($action)) {
            $middlewares = $this->parseMiddleware(
                $this->container->getProperty($action[0], 'middleware') ?? [],
                $action[1]
            );

            if (PHP_VERSION_ID >= 80000) {
                $annotations =
                    AnnotationManager::annotationMethod($action[0], $action[1], \Yng\Framework\Di\Annotations\Middleware::class);
                foreach ($annotations as $annotation) {
                    array_push($middlewares, ...$annotation->handle());
                }
            }

            return (new RequestHandler())
                ->setMiddlewares($middlewares)
                ->setRequestHandler(new ControllerDispatcher($action))
                ->handle($request);
        }
        throw new \Exception('Cannot resolve action.');
    }
}
