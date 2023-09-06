<?php
declare(strict_types = 1);

namespace Yng\Contract;

/**
 * Session驱动接口
 */
interface SessionHandlerInterface
{
    public function read(string $sessionId): string;
    public function delete(string $sessionId): bool;
    public function write(string $sessionId, string $data): bool;
}
