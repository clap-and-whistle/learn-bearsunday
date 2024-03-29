<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceObject;
use Cw\LearnBear\AppSpi\IdentityRepositoryInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Infrastructure\Form\QueryForNext;

class Login extends ResourceObject
{
    use QueryForNext;

    public function __construct(
        private readonly SessionHandlerInterface $cwSession,
        private readonly IdentityRepositoryInterface $identityRepository,
    ) {
    }

    public function onPost(string $username = '', string $password = ''): static
    {
        // 認証処理をする
        $uuid = $this->identityRepository->findByUserNameAndPassword($username, $password);
        if ($uuid === null) {
            $this->cwSession->setFlashMessage('ログイン認証に失敗しました', SessionHandlerInterface::FLASH_KEY_FOR_LOGIN_FORM);
            $toUrl = '/index';
            $this->code = Code::SEE_OTHER;
            $this->headers['Location'] = $toUrl;
            $this->body = [
                '_links' => [
                    'redirect' => ['href' => $toUrl],
                ],
            ];

            return $this;
        }

        // 認証されたPHPセッションを開始
        $this->cwSession->setAuth($uuid);

        // nextページを呼ぶ際に必要となるクエリ文字列（Next::onGet()の引数に相当）を準備
        $queryString = $this->getQueryStrForNext();

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
