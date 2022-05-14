<?php

namespace Yng\Framework\Http;

use Yng\Framework\Exceptions\HttpException;
use Yng\Framework\Http\Stream\File;
use Yng\Framework\Http\Stream\Html;
use Yng\Framework\Http\Stream\Json;
use Yng\Http\Message\Bags\HeaderBag;
use Yng\Http\Message\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;

class Response extends Psr7Response
{
    /**
     * @var array
     */
    protected array $cookies = [];

    /**
     * @param       $body
     * @param array $headers
     * @param       $statusCode
     */
    public function __construct($body = null, array $headers = [], $statusCode = 200)
    {
        $this->headers = new HeaderBag($headers);
        $this->body    = $body;
        $this->status  = $statusCode;
    }

    /**
     * 根据response类型自动创建响应对象
     *
     * @param null  $response
     * @param array $headers
     * @param int   $code
     *
     * @return static
     * @throws HttpException
     * @throws \Throwable
     */
    public static function make($response = '', array $headers = [], int $statusCode = 200)
    {
        if ($response instanceof ResponseInterface) {
            return $response;
        }
        if (is_array($response) || is_iterable($response)) {
            return Response::json($response, $headers, $statusCode);
        }
        if (is_null($response) || is_scalar($response)) {
            return Response::html((string)$response, $headers, $statusCode);
        }
        throw new HttpException('不支持的类型:' . gettype($response));
    }

    /**
     * 下载文件
     *
     * @param string $path
     * @param string $filename
     *
     * @return static
     */
    public static function download(string $path, string $filename = '')
    {
        $filename = $filename ?: pathinfo($path, PATHINFO_BASENAME);
        $headers  = [
            'Pragma'                    => 'public',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type'              => 'application/octet-stream',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename=' . $filename,
        ];
        return new static(new File($path), $headers);
    }

    /**
     * 返回一个html响应对象
     *
     * @param string $html
     * @param array  $headers
     * @param int    $statusCode
     *
     * @return static
     */
    public static function html($html = '', $headers = [], $statusCode = 200)
    {
        $headers['Content-Type'] = ['text/html; charset=utf-8'];
        return new static(new Html($html), $headers, $statusCode);
    }

    /**
     * 返回一个json响应对象
     *
     * @param array $json
     * @param array $headers
     * @param int   $statusCode
     *
     * @return static
     * @throws \Throwable
     */
    public static function json($json = [], $headers = [], $statusCode = 200)
    {
        $headers['Content-Type'] = ['application/json; charset=utf-8'];
        return new static(new Json($json), $headers, $statusCode);
    }

    /**
     * @param string $name
     * @param        $value
     *
     * @return \Yng\Http\Message\Message|void
     */
    public function header(string $name, $value)
    {
        return $this->withAddedHeader($name, $value);
    }

    /**
     * @param int $code
     *
     * @return Response|Psr7Response
     */
    public function code(int $code)
    {
        return $this->withStatus($code);
    }

    /**
     * @param string $location
     * @param        $code
     *
     * @return mixed
     */
    public function redirect(string $location, $code = 302)
    {
        return $this->withHeader('Location', $location)->withStatus($code);
    }

    /**
     * @param string $name
     * @param        $value
     * @param array  $options
     *
     * @return \Yng\Http\Message\Message|void
     */
    public function cookie(string $name, $value, array $options = [])
    {
        $this->cookies[$name] = new Cookie($name, $value, $options);

        return $this->withAddedHeader('Set-Cookie', $this->cookies[$name]->build());
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getCookie($name)
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }
}
