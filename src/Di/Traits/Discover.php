<?php

namespace Yng\Framework\Di\Traits;

trait Discover
{
    /**
     * 根据路径遍历文件，并使用$callback处理
     *
     * @param string  $dir
     * @param string  $baseDir
     * @param Closure $callback
     *
     * @return void
     */
    protected function discover(string $dir, string $baseDir, \Closure $callback)
    {
        foreach (glob(rtrim($dir, ' / ') . '/*') as $file) {
            if (is_file($file)) {
                $callback(str_replace('/', '\\', ucfirst(substr($file, strpos($file, $baseDir), -4))));
            } else {
                $this->discover($file, $baseDir, $callback);
            }
        }
    }
}
