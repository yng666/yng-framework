<?php

namespace Yng\Framework\Facades;

use Yng\Database\Contracts\ConnectorInterface;
use Yng\Database\Manager;
use Yng\Database\Query\Builder;
use Yng\Framework\Facade;

/**
 * @method static Builder table(string $table, ?string $alias = null)
 * @method static select(string $query, array $bindings = [], ?string $connection = null)
 * @method static exec(string $query, array $bindings = [], ?string $connection = null)
 * @method static delete(string $query, array $bindings = [], ?string $connection = null)
 * @method static insert(string $query, array $bindings = [], ?string $connection = null)
 * @method static ConnectorInterface connect(?string $name = null)
 * Class DB
 *
 * @package Yng\Framework\Facades
 */
class DB extends Facade
{
    protected static function getFacadeClass()
    {
        return Manager::class;
    }
}

