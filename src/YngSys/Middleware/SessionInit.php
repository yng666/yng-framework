<?php
declare(strict_types = 1);

namespace Yng\Middleware;

use Closure;
use Yng\App;
use Yng\Request;
use Yng\Response;
use Yng\Session;

/**
 * Session初始化
 */
class SessionInit
{
    public function __construct(protected App $app, protected Session $session)
    {
    }

    /**
     * Session初始化
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Session初始化
        $varSessionId = $this->app->config->get('session.var_session_id');
        $cookieName   = $this->session->getName();

        if ($varSessionId && $request->request($varSessionId)) {
            $sessionId = $request->request($varSessionId);
        } else {
            $sessionId = $request->cookie($cookieName);
        }

        if ($sessionId) {
            $this->session->setId($sessionId);
        }

        $this->session->init();

        $request->withSession($this->session);

        /** @var Response $response */
        $response = $next($request);

        $response->setSession($this->session);

        $this->app->cookie->set($cookieName, $this->session->getId(), $this->session->getConfig('expire'));

        return $response;
    }

    public function end(Response $response): void
    {
        $this->session->save();
    }
}
