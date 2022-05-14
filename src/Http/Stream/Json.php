<?php
declare(strict_types=1);

namespace Yng\Framework\Http\Stream;

use InvalidArgumentException;
use JsonSerializable;
use Psr\Http\Message\StreamInterface;

/**
 * JSON响应
 * Class Json
 *
 * @package Yng\Http\Response
 */
class Json implements StreamInterface
{

    /**
     * @var string
     */
    protected $stream;

    public function __construct($jsonSerializable, int $flags = 0, int $depth = 512)
    {
        if (is_array($jsonSerializable) || $jsonSerializable instanceof JsonSerializable) {
            try {
                // 返回JSON数据格式到客户端 包含状态信息
                $this->stream = json_encode($jsonSerializable, $flags, $depth);
                if (false === $this->stream) {
                    throw new \Exception(json_last_error_msg());
                }
            } catch (\Exception $e) {
                if ($e->getPrevious()) {
                    throw $e->getPrevious();
                }
                throw $e;
            }
        } else {
            throw new InvalidArgumentException('暂不支持的数据类型: ' . gettype($this->stream), 500);
        }
    }

    public function __toString()
    {
        return $this->stream;
    }

    public function close()
    {
        // TODO: Implement close() method.
    }

    public function detach()
    {
        // TODO: Implement detach() method.
    }

    public function getSize()
    {
        return strlen($this->stream);
    }

    public function tell()
    {
        // TODO: Implement tell() method.
    }

    public function eof()
    {
        // TODO: Implement eof() method.
    }

    public function isSeekable()
    {
        // TODO: Implement isSeekable() method.
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        // TODO: Implement seek() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    public function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    public function write($string)
    {
        // TODO: Implement write() method.
    }

    public function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    public function read($length)
    {
        // TODO: Implement read() method.
    }

    public function getContents()
    {
        return $this->stream;
    }

    public function getMetadata($key = null)
    {
        // TODO: Implement getMetadata() method.
    }

}
