<?php
declare(strict_types = 1);

namespace Yng\Initializer;

use Yng\App;

/**
 * 启动系统服务
 */
class BootService
{
    public function init(App $app)
    {
        $app->boot();
    }
}
