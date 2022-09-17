<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceObject;

class Index extends ResourceObject
{
    public function onGet(): static
    {
        $params = $this->body ?: [];
        $this->body = $params + [
            '_links' => [
                'login' => ['href' => '/login'],
            ],
        ];

        return $this;
    }
}
