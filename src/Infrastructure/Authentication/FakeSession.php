<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Authentication;

use Cw\LearnBear\AppSpi\SessionHandlerInterface;

class FakeSession implements SessionHandlerInterface
{
    private ?string $message = null;

    public function setAuth(string $uuid): void
    {
        $this->message = $uuid; // これはただのダミー処理。なんなら何もしなくても構わない。
    }

    public function isNotAuthorized(): bool
    {
        return false;
    }

    public function clearAuth(): void
    {
    }

    public function setFlashMessage(string $message, string $key): void
    {
        $this->message = empty($key)
            ? SessionHandlerInterface::DUMMY_MESSAGE
            : $message;
    }

    public function getFlashMessage(string $key): ?string
    {
        return $this->message ?? SessionHandlerInterface::DUMMY_MESSAGE;
    }

    public function destroy(): void
    {
    }
}
