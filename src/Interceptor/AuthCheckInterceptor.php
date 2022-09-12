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
use RuntimeException;

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
        $resourceObject = $invocation->getThis();
        if (! $resourceObject instanceof AuthBaseResourceObject) {
            // TODO: 後で消す（インターセプタ束縛を matcher->any() で設定してるところを直すときに）
            throw new RuntimeException('AuthBaseResourceObject にだけ使用できるインターセプターです');
        }

        // メソッド実行前の処理
        if ($this->cwSession->isNotAuthorized()) {
            return $this->makeError($resourceObject);
        }

        // メソッド実行
        $result = $invocation->proceed();

        // メソッド実行後の処理
        // TODO: 後で消す
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
