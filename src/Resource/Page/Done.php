<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Sunday\Inject\ResourceInject;
use Cw\LearnBear\Annotation\CheckAuth;
use Cw\LearnBear\Infrastructure\Form\QueryForNext;
use Cw\LearnBear\Infrastructure\Resource\AuthBaseResourceObject;
use Cw\LearnBear\Resource\App\Todo;

class Done extends AuthBaseResourceObject
{
    use QueryForNext;
    use ResourceInject;

    #[CheckAuth]
    public function onGet(int $id): static
    {
        $result = $this->resource
            ->put('app://self/todo', [
                'id' => $id,
                'status' => Todo::COMPLETE,
            ]);

        if ($result->code !== Code::ACCEPTED) {
            $this->code = $result->code;
            $this->setRendererForError();
            $this->body = [
                'status' => [
                    'code' => $this->code,
                    'message' => '更新に失敗しました',
                ],
            ];

            return $this;
        }

        $this->code = Code::SEE_OTHER;
        $this->headers['Location'] = '/next' . $this->getQueryStrForNext();

        return $this;
    }
}
