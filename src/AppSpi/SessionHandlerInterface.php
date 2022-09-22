<?php

declare(strict_types=1);

namespace Cw\LearnBear\AppSpi;

interface SessionHandlerInterface
{
    public const SESS_SEGMENT = self::class;

    public const FLASH_KEY_FOR_LOGIN_FORM = 'for_index';

    public function setAuth(string $uuid): void;

    public function isNotAuthorized(): bool;

    public function clearAuth(): void;

    public function setFlashMessage(string $message, string $key): void;

    public function getFlashMessage(string $key): ?string;

    public function destroy(): void;
}
