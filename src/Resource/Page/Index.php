<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceObject;
use DateTime;

class Index extends ResourceObject
{
    public function onGet(): static
    {
        // nextページを呼ぶ際に必要となるクエリ文字列（Next::onGet()の引数に相当）を準備
        $now = new DateTime();
        $year = $now->format('Y');
        $month = $now->format('n');
        $day = $now->format('j');
        $queryString = "?year={$year}&month={$month}&day={$day}";

        $params = $this->body ?: [];
        $this->body = $params + [
            '_links' => [
                'next' => ['href' => '/next' . $queryString],
            ],
        ];

        return $this;
    }
}
