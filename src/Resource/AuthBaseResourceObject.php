<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource;

use BEAR\Resource\ResourceObject;

abstract class AuthBaseResourceObject extends ResourceObject
{
    /**
     * @deprecated デバッグ用なので、プロダクトコードでは使用しないこと
     */
    public function setDebugMessage(string $msg): static
    {
        $this->body['debug'] = $msg;

        return $this;
    }
}
