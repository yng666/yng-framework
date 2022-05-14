<?php

namespace Yng\Framework\Console;

use Yng\Console\Console;

class Kernel extends Console
{
    protected array $buildIn = [
        'make'           => Commands\Make::class,
        'route'          => Commands\Route::class,
        'help'           => Commands\Help::class,
        'serve'          => Commands\Serve::class,
        'vendor:publish' => Commands\Vendor::class,
    ];
}
