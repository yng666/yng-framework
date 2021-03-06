<?php
declare(strict_types=1);

namespace Yng\Framework\Facades;

use Yng\Framework\Facade;
use Psr\Log\LoggerInterface;

/**
 * @method static emergency($message, array $context = [])
 * @method static alert($message, array $context = [])
 * @method static critical($message, array $context = [])
 * @method static error($message, array $context = [])
 * @method static warning($message, array $context = [])
 * @method static notice($message, array $context = [])
 * @method static info($message, array $context = [])
 * @method static debug($message, array $context = [])
 * @method static LoggerInterface get($name = 'default')
 * Class Log
 *
 * @package Yng\Framework\Facade
 */
class Logger extends Facade
{
    protected static function getFacadeClass()
    {
        return LoggerInterface::class;
    }
}
