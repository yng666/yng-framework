<?php

namespace Yng\Framework\Http\Stream;

use Psr\Http\Message\StreamInterface;

class Html implements StreamInterface
{
    /**
     * @var string
     */
    protected $stream;

    public function __construct(string $html = '')
    {
        $this->stream = $html;
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

    /**
     * @return int|null
     */
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
