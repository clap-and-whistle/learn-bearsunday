<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Ray\Di\AbstractModule;

class TestModule extends AbstractModule
{
    protected function configure(): void
    {
        $this->bind(SessionHandlerInterface::class)->toInstance($this->createDummySessionHandler());
    }

    public function createDummySessionHandler(): SessionHandlerInterface
    {
        return new class implements SessionHandlerInterface {
            private ?string $message = null;

            public function setAuth(string $uuid): void
            {
                $this->message = $uuid;
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
                $this->message = empty($key) ? null : $message;
            }

            public function getFlashMessage(string $key): ?string
            {
                return empty($key) ? null : $this->message;
            }
        };
    }
}
