<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Sunday\Inject\ResourceInject;
use Cw\LearnBear\Annotation\CheckAuth;
use Cw\LearnBear\Infrastructure\Form\QueryForNext;
use Cw\LearnBear\Infrastructure\Resource\AuthBaseResourceObject;
use DateTime;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\FormValidation;
use Ray\WebFormModule\FormInterface;

class Next extends AuthBaseResourceObject
{
    use QueryForNext;
    use ResourceInject;

    public FormInterface $todoForm;

    #[Inject]
    public function setForm(#[Named('todo_form')] FormInterface $todoForm): void
    {
        $this->todoForm = $todoForm;
    }

    #[CheckAuth]
    public function onGet(int $year, int $month, int $day): static
    {
        $params = ['year' => $year, 'month' => $month, 'day' => $day];
        $this->body = $params + [
            'weekday' => $this->resource->get('app://self/weekday', $params),
            'todo_form' => $this->todoForm,
            '_links' => [
                'logout' => ['href' => '/logout'],
            ],
        ];

        return $this;
    }

    public function onFailure(): static
    {
        $now = new DateTime();
        $this->code = Code::BAD_REQUEST;

        return $this->onGet(
            (int) $now->format('Y'),
            (int) $now->format('n'),
            (int) $now->format('j')
        );
    }

    /**
     * @param array<string, string> $todo
     */
    #[CheckAuth]
    public function onPost(array $todo = []): static
    {
        $this->createTodo($todo['title']);
        $this->code = Code::SEE_OTHER;
        $this->headers['Location'] = '/next' . $this->getQueryStrForNext();

        return $this;
    }

    /**
     * @FormValidation(form="todoForm", onFailure="onFailure")
     */
    public function createTodo(string $title): void
    {
        $request = $this->resource->post('app://self/todo', ['title' => $title]);
        $this->code = $request->code;
        $this->headers['Location'] = '/';
        $this['todo_form'] = $this->todoForm;
    }
}
