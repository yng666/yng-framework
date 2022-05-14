<?php
declare(strict_types=1);

namespace Yng\Framework\Http;

use Yng\{Framework\Http\Dispatcher\RouteDispatcher, Routing\RouteCollector};
use Yng\Http\Server\RequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @class   Kernel
 * @author  Yng
 * @date    2021/12/25
 * @time    10:55
 * @package Yng\Framework\Http
 */
class Kernel implements RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * 中间件
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * 中间件组
     *
     * @var array
     */
    protected array $middlewareGroups = [];

    /**
     * 服务提供者
     *
     * @var array
     */
    protected array $providers = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $container->register(...array_map([$container, 'make'], $this->providers));
        $container->boot();
        $this->container = $container;
    }

    /**
     * @return ServerRequestInterface
     */
    public function request(): ServerRequestInterface
    {
        ob_start();
        $request = Request::createFromGlobals();
        $this->container->set(ServerRequestInterface::class, $request);

        return $request;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function response(ServerRequestInterface $request): ResponseInterface
    {
        $exceptionHandler = config('http.exception_handler');
        if (!in_array($exceptionHandler, $this->middleware)) {
            array_unshift($this->middleware, $exceptionHandler);
        }
        return (new RequestHandler())
            ->setMiddlewares($this->middleware)
            ->setRequestHandler($this)
            ->handle($request);
    }

    /**
     * 获取http注册的中间件
     *
     * @param array $middlewares
     *
     * @return array
     */
    protected function getMiddlewares(array $middlewares): array
    {
        $parsedMiddleware = [];
        foreach ($middlewares as $key => $middleware) {
            if (\array_key_exists($middleware, $this->middlewareGroups)) {
                \array_push($parsedMiddleware, ...(array)$this->middlewareGroups[$middleware]);
            } else {
                $parsedMiddleware[] = $middleware;
            }
        }

        return $parsedMiddleware;
    }

    /**
     * @return \Closure
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $request->route = RouteCollector::resolve($request->getMethod(), $request->getUri()->getPath());
        $middlewares    = $this->getMiddlewares($request->route->getMiddlewares());

        return (new RequestHandler())
            ->setMiddlewares($middlewares)
            ->setRequestHandler(new RouteDispatcher($this->container))
            ->handle($request);
    }

    /**
     * 结束响应
     *
     * @param $response ResponseInterface
     *
     * @throws \Exception
     */
    public function end(ResponseInterface $response): void
    {
        if ($response->hasHeader('Set-Cookie')) {
            foreach ($response->getHeader('Set-Cookie') as $cookie) {
                \header('Set-Cookie: ' . $cookie, false);
            }
            $response = $response->withoutHeader('Set-Cookie');
        }
        foreach ($response->getHeaders() as $name => $headers) {
            \header($name . ': ' . \implode(', ', $headers));
        }

        \header(\sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()),
            true,
            $response->getStatusCode()
        );

        $body = $response->getBody();
        if (0 !== $body->getSize()) {
            echo $body;
        }
        ob_end_flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

}
