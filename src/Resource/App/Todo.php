<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\App;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceObject;
use Ray\AuraSqlModule\AuraSqlInject;

use function assert;
use function date;
use function is_object;

class Todo extends ResourceObject
{
    use AuraSqlInject;

    public const INCOMPLETE = 1;
    public const COMPLETE   = 2;

    public function onGet(int $id): static
    {
        $todo = $this->pdo->fetchOne('SELECT * FROM todo WHERE id = :id', ['id' => $id]);
        if (empty($todo)) {
            $this->code = Code::NOT_FOUND;

            return $this;
        }

        $todo['status_name'] = $this->isComplete((int) $todo['status']) ? '完了' : '未完了';
        $this->body = $this->body ?: [];
        $this->body['todo'] = $todo;

        return $this;
    }

    private function isComplete(int $statusValue): bool
    {
        return $statusValue === self::COMPLETE;
    }

    public function onPost(string $title): static
    {
        $sql = 'INSERT INTO todo (title, status, created, updated) VALUES (:title, :status, :created, :updated)';
        $bind = [
            'title' => $title,
            'status' => self::INCOMPLETE,
            'created' => date('Y-m-d H:i:s'),
            'updated' => date('Y-m-d H:i:s'),
        ];

        $statement = $this->pdo->prepare($sql);
        assert(is_object($statement));
        $statement->execute($bind);

        $id = $this->pdo->lastInsertId();

        $this->code = Code::CREATED;
        $this->headers['Location'] = "/todo/?id={$id}";

        return $this;
    }

    public function onPut(int $id, int $status): static
    {
        $sql = 'UPDATE todo SET status = :status WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        assert(is_object($statement));
        $statement->execute([
            'id' => $id,
            'status' => $status,
        ]);
        $this->code = Code::ACCEPTED;
        $this->headers['Location'] = "/todo/?id={$id}";

        return $this;
    }

    public function onDelete(int $id): static
    {
        $sql = 'DELETE FROM todo WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        assert(is_object($statement));
        $statement->execute(['id' => $id]);

        return $this;
    }
}
