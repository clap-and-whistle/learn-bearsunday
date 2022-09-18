<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceObject;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;

class Logout extends ResourceObject
{
    public function __construct(
        private readonly SessionHandlerInterface $cwSession,
    ) {
    }

    public function onGet(): ResourceObject
    {
        // 認証されたPHPセッションを終了
        $this->cwSession->clearAuth();

        $this->body = $this->body ?: [];
        $this->body['_links'] = [
            'index' => ['href' => '/index'],
        ];

        return $this;
    }
}
