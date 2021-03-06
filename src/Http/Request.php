<?php

namespace Yng\Framework\Http;

use Yng\Http\Message\Bags\FileBag;
use Yng\Http\Message\Bags\HeaderBag;
use Yng\Http\Message\Bags\InputBag;
use Yng\Http\Message\Bags\ParameterBag;
use Yng\Http\Message\Bags\ServerBag;
use Yng\Http\Message\ServerRequest as Psr7Request;
use Yng\Http\Message\Uri;
use Yng\Routing\Route;

/**
 * @class   Request
 * @author  Yng
 * @date    2022/04/23
 * @time    9:29
 * @package Yng\Framework\Http
 */
class Request extends Psr7Request
{
    /**
     * @var Route|null
     */
    public ?Route $route = null;

    /**
     * @return Request
     */
    public static function createFromGlobals()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (0 === strcasecmp('HTTP_', mb_substr($key, 0, 5))) {
                $headers[str_replace('_', '-', mb_substr($key, 5))] = $value;
            }
        }

        return (new static())->initialize($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER, $headers);
    }

    /**
     * @param Request $from
     * @param         $to
     *
     * @return Request
     */
    public static function createFrom(self $from, $to = null)
    {
        $request = $to ?: new static;

        return $request->initialize(
            $from->getQueryParams(),
            $from->getParsedBody(),
            $from->getCookieParams(),
            $from->getUploadedFiles(),
            $from->getServerParams(),
            $from->getHeaders(),
            $from->getAttributes(),
        );
    }

    /**
     * @param array $queryParams
     * @param array $post
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param array $headers
     *
     * @return $this
     */
    public function initialize(
        array $queryParams = [],
        array $parsedBody = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        array $headers = [],
        array $attributes = []
    )
    {
        $this->queryParams   = new InputBag($queryParams);
        $this->parsedBody    = new InputBag($parsedBody);
        $this->cookieParams  = new InputBag($cookies);
        $this->uploadedFiles = new FileBag($files);
        $this->serverParams  = new ServerBag($server);
        $this->headers       = new HeaderBag($headers ?: $this->serverParams->getHeaders());
        $this->attributes    = new ParameterBag($attributes);
        $this->method        = $this->serverParams->get('REQUEST_METHOD');
        $this->uri           = new Uri($this->url(true));

        return $this;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function header(string $name)
    {
        return $this->getHeaderLine($name);
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function server(string $name)
    {
        return $this->serverParams->get(strtoupper($name));
    }

    /**
     * ??????????????????
     *
     * @param string $method
     * ????????????
     *
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return 0 === strcasecmp($method, $this->getMethod());
    }

    /**
     * ???????????????url
     *
     * @param bool|null $full
     *
     * @return string
     */
    public function url(bool $full = false): string
    {
        $scheme = (0 === strcasecmp('on', (string)$this->serverParams->get('HTTPS')) ||
            0 === strcasecmp('https', (string)$this->serverParams->get('HTTP_X_FORWARDED_PROTO'))) ? 'https' : 'http';

        $parts = parse_url($this->serverParams->get('REQUEST_URI'));
        $path  = '/';
        $query = '';
        if ($full) {
            if (isset($parts['path'])) {
                $path = '/' . trim($parts['path'], '/');
            }
            if (isset($parts['query'])) {
                $query = '?' . $parts['query'];
            } else if ($queryString = $this->serverParams->get('QUERY_STRING')) {
                $query = '?' . $queryString;
            }
        }

        return sprintf(
            '%s://%s%s%s',
            $scheme,
            $this->getHeaderLine('HOST'),
            $path,
            $query,
        );
    }

    /**
     * ???????????????????????????ip
     *
     * @return bool|mixed|string
     */
    public function ip()
    {
        if (isset($this->ip)) {
            return $this->ip;
        }
        $ip = false;
        // ?????????IP ??? NONE
        if (!is_null($this->server('HTTP_CLIENT_IP'))) {
            $ip = $this->server('HTTP_CLIENT_IP');
        }
        // ??????????????????????????????????????????IP????????????????????????,??????????????????????????????????????????
        if (!is_null($this->server('HTTP_X_FORWARDED_FOR'))) {
            $ips = explode(', ', $this->server('HTTP_X_FORWARDED_FOR'));
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match('/^(10???172.16???192.168)./', $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        // ?????????IP ??? (????????????)??????????????? IP
        $this->ip = ($ip ?: $this->server('REMOTE_ADDR'));

        return $this->ip;
    }

    /**
     * ??????cookie
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function cookie(string $name)
    {
        return $this->cookieParams->get(strtoupper($name));
    }

    /**
     * ????????????ajax??????
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return 0 === strcasecmp('XMLHttpRequest', $this->getHeaderLine('X-REQUESTED-WITH'));
    }

    /**
     * ??????????????????????????????????????????????????????
     *
     * @param string $path
     *
     * @return bool
     */
    public function is(string $path): bool
    {
        $requestPath = $this->getUri()->getPath();

        return 0 === strcasecmp($requestPath, $path) || preg_match("#^{$path}$#iU", $requestPath);
    }

    /**
     * get????????????
     *
     * @param string|array $key
     * ?????????????????????
     * @param string|array $default
     * ???????????????????????????
     *
     * @return array|string
     */
    public function get($key = null, $default = null)
    {
        return $this->input($key, $default, $this->queryParams->all());
    }

    /**
     * ??????post??????
     *
     * @param string|array $key     ?????????????????????
     * @param string|int   $default ???????????????????????????
     *
     * @return array|string
     */
    public function post($key = null, $default = null)
    {
        return $this->input($key, $default, $this->parsedBody->all());
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->parsedBody->all() + $this->getQueryParams();
    }

    /**
     * ?????????????????????????????????
     *
     * @param array $haystack
     * @param       $needle
     *
     * @return bool
     */
    protected function isEmpty(array $haystack, $needle)
    {
        return !isset($haystack[$needle]) || '' === $haystack[$needle];
    }

    /**
     * @param array             $input
     * @param string|array|null $needle
     * @param string|null       $default
     *
     * @return array|mixed|null
     */
    public function input($key = null, $default = null, ?array $from = null)
    {
        $from ??= $this->all();

        if (is_null($key)) {
            return $from ?? [];
        }
        if (is_scalar($key)) {
            return $this->isEmpty($from, $key) ? $default : $from[$key];
        }
        if (is_array($key)) {
            $return = [];
            foreach ($key as $value) {
                $return[$value] = $this->isEmpty($from, $value) ? ($default[$value] ?? null) : $from[$value];
            }

            return $return;
        }
        throw new \InvalidArgumentException('InvalidArgument???');
    }

    /**
     * $_FILES ????????????
     *
     * @param string $field
     *
     * @return UploadedFile|null
     */
    public function file(string $field)
    {
        return $this->uploadedFiles->get($field);
    }

    /**
     * __get
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes->get($key);
    }

    /**
     * __set
     *
     * @param $key
     * @param $value
     *
     * @throws \Exception
     */
    public function __set($key, $value)
    {
        $this->attributes->set($key, $value);
    }
}
