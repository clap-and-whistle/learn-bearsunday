<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceObject;

class Logout extends ResourceObject
{
    public function onGet(): ResourceObject
    {
        $this->body = $this->body ?: [];
        $this->body['_links'] = [
            'index' => ['href' => '/index'],
        ];

        return $this;
    }
}
