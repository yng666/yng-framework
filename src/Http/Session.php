<?php

namespace Yng\Framework\Http;

use Yng\Config\Repository;

class Session extends \Yng\Session\Session
{
    /**
     * @param Repository $repository
     *
     * @return static
     */
    public static function __new(Repository $repository)
    {
        return new static($repository->get('session'));
    }
}
