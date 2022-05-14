<?php
declare(strict_types=1);

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;
use Yng\Http\Request\Uri;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @method static bool isMethod(string $method) 请求方式判断
 * @method static string getMethod() 当前的请求方式
 * @method static string path() 请求的路径
 * @method static Uri uri() UriInterface对象
 * @method static string url(bool $full = null)  请求的地址
 * @method static bool isAjax() 判断是否ajax请求
 * @method static string|array server(string $name = null) 获取$_SERVER
 * @method static string|array header(string $header = null) 获取header
 * @method static string ip()
 * @method static array|string|null get($field = null, $default = null) 获取GET的参数
 * @method static array|string|null post($field = null, $default = null) 获取POST的参数
 * @method static array|string|null input($field = null, $default = null) 获取$_REQUEST参数
 * @method static string|false raw() 获取提交的原始数据
 * @method static array|string|null file($field = null, $default = '')
 * @method static string cookie($field = null, $default = '')
 * Class Request
 * @package Yng\Framework\Facades
 */
class Request extends Facade
{
    protected static function getFacadeClass()
    {
        return ServerRequestInterface::class;
    }
}
