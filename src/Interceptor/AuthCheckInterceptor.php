<?php

declare(strict_types=1);

namespace Cw\LearnBear\Interceptor;

use BEAR\Resource\Code;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Infrastructure\Resource\AuthBaseResourceObject;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

use function assert;

class AuthCheckInterceptor implements MethodInterceptor
{
    public const AUTH_ERROR_MESSAGE = 'ユーザー認証をしてください';

    public function __construct(private readonly SessionHandlerInterface $cwSession)
    {
    }

    /**
     * @inheritDoc
     */
    public function invoke(MethodInvocation $invocation)
    {
        // メソッド実行前の処理
        if ($this->cwSession->isNotAuthorized()) {
            $resourceObject = $invocation->getThis();
            assert($resourceObject instanceof AuthBaseResourceObject);

            return $this->makeError($resourceObject);
        }

        // メソッド実行
        $result = $invocation->proceed();

        // メソッド実行後の処理
        if ($result instanceof AuthBaseResourceObject) {
            $result->setDebugMessage('認証済み確認後の処理を通過');
        }

        return $result;
    }

    private function makeError(AuthBaseResourceObject $resourceObj): AuthBaseResourceObject
    {
        $resourceObj->setRendererForError();
        $resourceObj->code = Code::UNAUTHORIZED;
        $resourceObj->body = [
            'status' => [
                'code' => $resourceObj->code,
                'message' => self::AUTH_ERROR_MESSAGE,
            ],
        ];

        return $resourceObj;
    }
}
