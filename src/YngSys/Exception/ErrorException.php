<?php
declare(strict_types = 1);

namespace Yng\Exception;

use Yng\Exception;

/**
 * YngPHP错误异常
 * 主要用于封装 set_error_handler 和 register_shutdown_function 得到的错误
 * 除开从 Yng\Exception 继承的功能
 * 其他和PHP系统\ErrorException功能基本一样
 */
class ErrorException extends Exception
{
    /**
     * 用于保存错误级别
     * @var integer
     */
    protected $severity;

    /**
     * 错误异常构造函数
     * @access public
     * @param  integer $severity 错误级别
     * @param  string  $message  错误详细信息
     * @param  string  $file     出错文件路径
     * @param  integer $line     出错行号
     */
    public function __construct(int $severity, string $message, string $file, int $line)
    {
        $this->severity = $severity;
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
        $this->code     = 0;
    }

    /**
     * 获取错误级别
     * @access public
     * @return integer 错误级别
     */
    final public function getSeverity()
    {
        return $this->severity;
    }
}
