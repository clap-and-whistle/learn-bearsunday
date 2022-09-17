<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceObject;
use Cw\LearnBear\AppSpi\LoggerInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;

use function var_export;

class Index extends ResourceObject
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SessionHandlerInterface $cwSession,
    ) {
    }

    public function onGet(): static
    {
        $flashMsg = $this->cwSession->getFlashMessage(SessionHandlerInterface::FLASH_KEY_FOR_LOGIN_FORM);
        $params = $this->body ?: [];
        if ($flashMsg) {
            $params['flash_message'] = $flashMsg;
        }

        $this->body = $params + [
            '_links' => [
                'login' => ['href' => '/login'],
            ],
        ];
        $this->logger->log(var_export([__METHOD__ . ': body' => $this->body], true));

        return $this;
    }
}
