<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\RenderInterface;
use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Inject\ResourceInject;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Ray\Di\Di\Named;

class Next extends ResourceObject
{
    use ResourceInject;

    public function __construct(
        #[Named('error_page')] private readonly RenderInterface $errorRender,
        private readonly SessionHandlerInterface $cwSession,
    ) {
    }

    public function onGet(int $year, int $month, int $day): static
    {
        // 認証されたPHPセッション継続をチェック
        if ($this->cwSession->isNotAuthorized()) {
            $this->code = Code::UNAUTHORIZED;
            $this->setRenderer($this->errorRender);
            $this->body = [
                'status' => [
                    'code' => $this->code,
                    'message' => 'ユーザー認証をしてください',
                ],
            ];

            return $this;
        }

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
