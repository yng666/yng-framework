<?php

namespace Yng\Framework\Http\Middleware;

use Yng\Framework\App;
use Yng\Framework\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionInit implements MiddlewareInterface
{
    /**
     * @var Session
     */
    protected Session $session;

    /**
     * @var string|array|mixed|null
     */
    protected string $sessionName;

    /**
     * Cookie 过期时间
     *
     * @var array|mixed|null
     */
    protected $cookieExpires;

    /**
     * @var App
     */
    protected App $app;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->session       = $app->make(Session::class);
        $this->sessionName   = $app->config->get('session.name', 'MAXPHP_SESSION_ID');
        $this->cookieExpires = $app->config->get('session.cookie_expire', 3600);
        $this->app           = $app;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $id = $request->getCookieParams()[$this->sessionName] ?? null;
        if (is_null($id)) {
            $id = $this->session->refreshId();
        } else {
            $this->session->setId($id);
        }
        $this->session->initialize();
        $response = $handler->handle($request);
        $this->session->save();
        return $response->withAddedHeader('Set-Cookie', "$this->sessionName=$id; expires=$this->cookieExpires");
    }
}
