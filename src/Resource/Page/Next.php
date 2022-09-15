<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Sunday\Inject\ResourceInject;
use Cw\LearnBear\Annotation\CheckAuth;
use Cw\LearnBear\Infrastructure\Resource\AuthBaseResourceObject;

class Next extends AuthBaseResourceObject
{
    use ResourceInject;

    #[CheckAuth]
    public function onGet(int $year, int $month, int $day): static
    {
        $params = ['year' => $year, 'month' => $month, 'day' => $day];
        $this->body = $params + [
            'weekday' => $this->resource->get('app://self/weekday', $params),
            '_links' => [
                'logout' => ['href' => '/logout'],
            ],
        ];

        return $this;
    }
}
