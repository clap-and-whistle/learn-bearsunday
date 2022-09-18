<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\App;

use BEAR\Resource\ResourceObject;
use Ray\AuraSqlModule\AuraSqlInject;

class TodoList extends ResourceObject
{
    use AuraSqlInject;

    public function onGet(?int $status = null): static
    {
        $this->body = $status === null
            ? $this->pdo->fetchAll('SELECT * FROM todo')
            : $this->pdo->fetchAll('SELECT * FROM todo WHERE status = :status', ['status' => $status]);

        return $this;
    }
}
