<?php

use Yng\{Framework\App, Framework\Http\Session, Framework\View, Utils\Collection, Utils\Filesystem};
use Yng\Framework\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

if (false === \function_exists('app')) {
    /**
     * 容器实例化和获取实例
     *
     * @param string|null $id
     * @param array       $arguments
     * @param bool        $renew
     *
     * @return App
     */
    function app()
    {
        return App::getInstance();
    }
}

if (false === \function_exists('invoke')) {
    /**
     * 容器调用方法
     *
     * @param array|Closure $callback
     * 数组或者闭包
     * @param array         $arguments
     * 给方法传递的参数列表
     * @param bool          $renew
     * 重新实例化，仅$callable为数组时候生效
     * @param array         $constructorParameters
     * 构造函数参数数组，仅$callable为数组时候生效
     *
     * @return mixed
     * @throws Exception
     */
    function invoke(callable $callback, array $arguments = [])
    {
        if (is_array($callback)) {
            return \app()->invokeMethod($callback, $arguments);
        }
        if ($callback instanceof Closure) {
            return \app()->invokeFunc($callback, $arguments);
        }
        throw new ContainerException('Cannot invoke method.');
    }
}

if (false === \function_exists('make')) {
    /**
     * @param string $id
     * @param array  $arguments
     *
     * @return mixed
     */
    function make(string $id, array $arguments = [])
    {
        return \app()->make($id, $arguments);
    }
}

if (false === \function_exists('resolve')) {
    function resolve($id, array $argumens = [])
    {
        return \app()->resolve($id, $argumens);
    }
}

if (false === \function_exists('abort')) {
    /**
     * 抛出异常
     *
     * @param string $message
     * @param int    $code
     * @param string $class
     * @param null   $options
     */
    function abort(string $message, int $code = 0, string $class = \Exception::class, $options = null)
    {
        throw new $class($message, $code, $options);
    }
}

if (false === \function_exists('config')) {
    /**
     *配置文件获取辅助函数
     *
     * @param $key
     * 配置文件名
     *
     * @return mixed
     */
    function config(string $key = null, $default = null)
    {
        $config = \make(\Yng\Config\Repository::class);

        return $key ? $config->get($key, $default) : $config->all();
    }
}

if (false === \function_exists('env')) {
    /**
     * env获取
     *
     * @param string|null $key
     * @param null        $default
     *
     * @return mixed
     * @throws Exception
     */
    function env(string $key = null, $default = null)
    {
        return \make(\Yng\Env\Env::class)->get($key, $default);
    }
}

if (false === \function_exists('collect')) {
    /**
     * 返回一个数据集对象
     *
     * @param mixed $items
     * 数组或者返回数组的闭包
     *
     * @return Collection
     */
    function collect($items = []): Collection
    {
        return new Collection($items);
    }
}

if (false === \function_exists('filesystem')) {
    /**
     * @return Filesystem
     */
    function filesystem(): Filesystem
    {
        return \make(Filesystem::class);
    }
}

if (false === \function_exists('retry')) {
    /**
     * @param Closure $call
     * 重试逻辑
     * @param false   $whenReturns
     * 返回该值时会触发重试
     * @param int     $max
     * 最大执行次数
     * @param int     $sleep
     * 休眠时间
     * @param null    $fallback
     * 异常回调
     *
     * @return mixed
     */
    function retry(Closure $call, $whenReturns = false, int $times = 2, $sleep = 0, $fallback = null)
    {
        return \Yng\Retry::whenReturns($whenReturns)
                         ->max($times)
                         ->sleep($sleep)
                         ->fallback($fallback)
                         ->call($call);
    }
}

if (false === \function_exists('event')) {
    /**
     * 事件助手函数
     *
     * @param object $event
     *
     * @return object
     */
    function event(object $event): object
    {
        return \make(\Yng\Event\EventDispatcher::class)->dispatch($event);
    }
}

if (false === \function_exists('base_path')) {
    /**
     * @return string
     * @throws Exception
     */
    function base_path(string $path)
    {
        return env('root_path') . ltrim($path, '/');
    }
}

if (false === \function_exists('public_path')) {
    /**
     * public 目录
     *
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    function public_path(string $path = '')
    {
        return env('public_path') . ltrim($path, '/');
    }
}

if (false === \function_exists('storage_path')) {
    /**
     * storage 目录
     *
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    function storage_path(string $path = '')
    {
        return env('storage_path') . ltrim($path, '/');
    }
}

if (false === \function_exists('route_path')) {
    /**
     * 路由地址
     *
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    function route_path(string $path = '')
    {
        return env('route_path') . ltrim($path, '/');
    }
}

if (false === \function_exists('translate')) {
    /**
     * 本地化
     *
     * @param string      $key
     * @param string|null $locale
     *
     * @return array|ArrayAccess|mixed
     */
    function translate(string $key, ?string $locale = null)
    {
        return make(\Yng\Lang::class)->translate($key, $locale);
    }
}


if (false === \function_exists('url')) {
    /**
     * url生成
     *
     * @param string $name
     * @param array  $args 关联数组
     *
     * @return string
     * @throws \Exception
     */
    function url(string $name, array $args = []): string
    {
        return \Yng\Routing\Url::build($name, $args);
    }
}

if (false === \function_exists('apache_request_headers')) {
    /**
     * 兼获取Headers的方法
     *
     * @return array
     */
    function apache_request_headers(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $server) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $headers[str_replace('_', '-', substr($key, 5))] = $server;
            }
        }
        return $headers;
    }
}

if (false === \function_exists('redirect')) {

    /**
     * 重定向
     *
     * @param string $url
     * @param int    $code
     */
    function redirect(string $url, int $code = 302)
    {
        return make(ResponseInterface::class)
            ->withStatus($code)
            ->withHeader('Location', $url);
    }
}

if (false === \function_exists('response')) {
    /**
     * @param string $response
     * @param array  $header
     * @param int    $code
     *
     * @return ResponseInterface
     * @throws Throwable
     * @throws \Yng\Http\Exceptions\HttpException
     */
    function response($response = '', array $header = [], $code = 200): ResponseInterface
    {
        return Response::make($response, $header, $code);
    }

}

if (false === \function_exists('json')) {
    /**
     * @param array $jsonSerializable
     *
     * @return ResponseInterface
     */
    function json(array $jsonSerializable): ResponseInterface
    {
        return Response::json($jsonSerializable);
    }
}

if (false === \function_exists('request')) {
    /**
     * @return ServerRequestInterface
     */
    function request()
    {
        return make(ServerRequestInterface::class);
    }
}

if (false === \function_exists('session')) {
    /**
     * @param      $name
     * @param null $value
     *
     * @return mixed
     */
    function session($name, $value = null)
    {
        /** @var Session $session */
        $session = make(Session::class);
        if (isset($value)) {
            return $session->set($name, $value);
        }
        return $session->get($name);
    }
}

if (false === \function_exists('view')) {
    /**
     * @param string $template
     * @param array  $arguments
     *
     * @return ResponseInterface
     * @throws Throwable
     * @throws \Yng\Framework\Exceptions\HttpException
     */
    function view(string $template, array $arguments = []): ResponseInterface
    {
        /** @var View $renderer */
        $renderer = make(View::class);

        return $renderer->render($template, $arguments);
    }
}

if (false === \function_exists('cache')) {
    /**
     * @return \Yng\Framework\Cache
     */
    function cache()
    {
        return make(\Yng\Framework\Cache::class);
    }
}
