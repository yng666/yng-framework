<?php

namespace Yng\Framework\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AllowCrossDomain implements MiddlewareInterface
{
    /**
     * 全局跨域
     *
     * @var bool
     */
    protected bool $global = false;

    /**
     * 允许域
     *
     * @var array
     */
    protected array $allowOrigin = [];

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($this->global) {
            $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        } else {
            $origin = \array_merge($request->route->getAllowCrossDomain(), $this->allowOrigin);
            if (in_array('*', $origin)) {
                $response = $response->withHeader('Access-Control-Allow-Origin', '*');
            } else if (in_array($allow = $request->getHeaderLine('Origin'), $origin)) {
                $response = $response->withHeader('Access-Control-Allow-Origin', $allow);
            }
            if ($request->isMethod('OPTIONS')) {
                $response = $response->withStatus('204');
            }
        }
        return $response;
    }
}
