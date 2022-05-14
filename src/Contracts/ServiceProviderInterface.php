<?php

namespace Yng\Framework\Contracts;

interface ServiceProviderInterface
{
    public function register();
    public function boot();
}
