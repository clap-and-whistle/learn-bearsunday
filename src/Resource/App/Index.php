<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\App;

use BEAR\Resource\ResourceObject;

class Index extends ResourceObject
{
    public function onGet(): static
    {
        $this->body = ['hello' => 'world'];

        return $this;
    }
}
