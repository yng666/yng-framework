<?php

namespace Yng\Framework\Http\Dispatcher;

use Yng\Di\AnnotationManager;
use Yng\Framework\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yng\Cache\Annotations\Cacheable;

class ControllerDispatcher implements RequestHandlerInterface
{
    protected $action;

    public function __construct($action)
    {
        $this->action = $action;
    }

    /**
     * @param array $callable
     * @param array $params
     *
     * @return Response
     * @throws \Yng\Framework\Exceptions\HttpException
     * @throws \Throwable
     */
    protected function responseFromController(array $callable, array $params = [])
    {
        return Response::make(app()->invokeMethod($callable, array_filter($params, function($value) {
            return !is_null($value);
        })));
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeParams = $request->route->getParameters();

        if (PHP_VERSION_ID >= 80000) {
            $annotations = AnnotationManager::annotationMethod($this->action[0], $this->action[1], Cacheable::class);
            if (!empty($annotations)) {
                $key = implode(':', $this->action) . ':' . implode(':', $routeParams);
                return $annotations[0]->handle($key, function() use ($routeParams) {
                    return $this->responseFromController($this->action, $routeParams);
                });
            }
        }

        return $this->responseFromController($this->action, $routeParams);
    }
}
