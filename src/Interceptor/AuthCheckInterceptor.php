<?php

declare(strict_types=1);

namespace Cw\LearnBear\Interceptor;

use BEAR\Resource\Code;
use BEAR\Resource\RenderInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Resource\AuthBaseResourceObject;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Named;

use function assert;

class AuthCheckInterceptor implements MethodInterceptor
{
    public function __construct(
        private readonly SessionHandlerInterface $cwSession,
        #[Named('error_page')] private readonly RenderInterface $errorRenderer
    ) {
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
        $resourceObj->setRenderer($this->errorRenderer);
        $resourceObj->code = Code::UNAUTHORIZED;
        $resourceObj->body = [
            'status' => [
                'code' => $resourceObj->code,
                'message' => 'ユーザー認証をしてください',
            ],
        ];

        return $resourceObj;
    }
}
