<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Inject\ResourceInject;

class Next extends ResourceObject
{
    use ResourceInject;

    public function onGet(int $year, int $month, int $day): static
    {
        $params = ['year' => $year, 'month' => $month, 'day' => $day];
        $this->body = $params + [
            'weekday' => $this->resource->get('app://self/weekday', $params),
            '_links' => [
                'index' => ['href' => '/index'],
            ],
        ];

        return $this;
    }
}
