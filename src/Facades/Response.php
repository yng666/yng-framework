<?php
declare(strict_types=1);

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @method static \Yng\Framework\Http\Response withBody(StreamInterface $stream)
 * @method static \Yng\Framework\Http\Response withStatus(int $code)
 * @method static \Yng\Framework\Http\Response withHeader($name, $value)
 * @method static \Yng\Framework\Http\Response download(string $path, string $fileName = '')
 * Class Config
 *
 * @package Yng\Framework\Facades
 */
class Response extends Facade
{
    protected static function getFacadeClass()
    {
        return ResponseInterface::class;
    }
}
