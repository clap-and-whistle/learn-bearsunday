<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceObject;
use Cw\LearnBear\AppSpi\LoggerInterface;
use DateTime;

use function password_hash;

use const PASSWORD_BCRYPT;

class Login extends ResourceObject
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onPost(string $username = '', string $password = ''): static
    {
        $this->logger->log('username: ' . $username . ', password: ' . password_hash($password, PASSWORD_BCRYPT));

        // nextページを呼ぶ際に必要となるクエリ文字列（Next::onGet()の引数に相当）を準備
        $now = new DateTime();
        $year = $now->format('Y');
        $month = $now->format('n');
        $day = $now->format('j');
        $queryString = "?year={$year}&month={$month}&day={$day}";

        $toUrl = '/next' . $queryString;
        $this->code = Code::SEE_OTHER;
        $this->headers['Location'] = $toUrl;

        $params = $this->body ?: [];
        $this->body = $params + [
            '_links' => [
                'redirect' => ['href' => $toUrl],
            ],
        ];

        return $this;
    }
}
