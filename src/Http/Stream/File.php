<?php

namespace Yng\Framework\Http\Stream;

use Psr\Http\Message\StreamInterface;

class File implements StreamInterface
{

    protected $path;

    protected $handle;

    public function __construct($path)
    {
        $this->path   = $path;
        $this->handle = fopen($path, 'r');
    }

    public function __toString()
    {
        while (!$this->eof()) {
            echo fread($this->handle, 1024);
        }
        $this->close();
        return '';
    }

    public function close()
    {
        return fclose($this->handle);
    }

    public function detach()
    {
        // TODO: Implement detach() method.
    }

    public function getSize()
    {
        return filesize($this->path);
    }

    public function tell()
    {
        return ftell($this->handle);
    }

    public function eof()
    {
        return feof($this->handle);
    }

    public function isSeekable()
    {
        // TODO: Implement isSeekable() method.
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->handle, $offset, $whence);
    }

    public function rewind()
    {
        return rewind($this->handle);
    }

    public function isWritable()
    {
        return is_writable($this->path);
    }

    public function write($string)
    {
        return fwrite($this->handle, $string);
    }

    public function isReadable()
    {
        return is_readable($this->path);
    }

    public function read($length)
    {
        return fread($this->handle, $length);
    }

    public function getContents()
    {
        return readfile($this->path);
    }

    public function getMetadata($key = null)
    {
        // TODO: Implement getMetadata() method.
    }

}
