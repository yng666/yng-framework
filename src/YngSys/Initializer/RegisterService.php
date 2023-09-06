<?php
declare(strict_types = 1);

namespace Yng\Initializer;

use Yng\App;
use Yng\Service\ModelService;
use Yng\Service\PaginatorService;
use Yng\Service\ValidateService;

/**
 * 注册系统服务
 */
class RegisterService
{

    protected $services = [
        PaginatorService::class,
        ValidateService::class,
        ModelService::class,
    ];

    /**
     * 初始化服务
     */
    public function init(App $app)
    {
        $file = $app->getRootPath() . 'vendor/services.php';

        $services = $this->services;

        if (is_file($file)) {
            $services = array_merge($services, include $file);
        }

        foreach ($services as $service) {
            if (class_exists($service)) {
                $app->register($service);
            }
        }
    }
}
