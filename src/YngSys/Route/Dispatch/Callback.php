<?php
declare(strict_types = 1);

namespace Yng\Route\Dispatch;

use Yng\Route\Dispatch;

/**
 * 回调调度
 */
class Callback extends Dispatch
{
    public function exec()
    {
        // 执行回调方法
        $vars = array_merge($this->request->param(), $this->param);

        return $this->app->invoke($this->dispatch, $vars);
    }
}
