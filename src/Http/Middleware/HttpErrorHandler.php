<?php

namespace Yng\Framework\Http\Middleware;

use ErrorException;
use Yng\Framework\Exceptions\HttpException;
use Yng\Framework\Http\Response;
use Yng\Routing\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * @class   HttpErrorHandler
 * @author  Yng
 * @date    2021/12/20
 * @time    13:10
 * @package Yng\Framework\Http\Middleware
 */
class HttpErrorHandler implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler($this->handleError());
        try {
            $response = $handler->handle($request);
        } catch (Throwable $throwable) {
            $response = $this->handleThrowable($throwable, $request);
        }
        restore_error_handler();

        return $response;
    }

    /**
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     *
     * @return void
     */
    protected function handleError(): \Closure
    {
        return function(int $errno, string $errstr, string $errfile, int $errline) {
            if (error_reporting() & $errno) {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        };
    }

    /**
     * @param Throwable              $throwable
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    protected function reportException(Throwable $throwable, ServerRequestInterface $request)
    {

    }

    /**
     * @param Throwable              $throwable
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    protected function handleThrowable(Throwable $throwable, ServerRequestInterface $request): ResponseInterface
    {
        $this->reportException($throwable, $request);

        return $this->renderException($throwable, $request);
    }

    /**
     * @param Throwable              $throwable
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws HttpException
     * @throws Throwable
     */
    protected function renderException(Throwable $throwable, ServerRequestInterface $request): ResponseInterface
    {
        try {
            ob_start();
            include base_path('vendor/yng/framework/resources/error/trace.tpl');
            $response = ob_get_clean();
        } catch (Throwable $throwable) {
            $response = $throwable->getMessage();
        }
        return Response::make($response, [], $this->getCode($throwable));
    }

    /**
     * HttpCode
     *
     * @param Throwable $throwable
     *
     * @return int|mixed
     */
    protected function getCode(Throwable $throwable, int $default = 400)
    {
        return $throwable instanceof HttpException || $throwable instanceof RouteNotFoundException ? $throwable->getCode() : $default;
    }
}
