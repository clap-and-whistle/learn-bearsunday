<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Resource;

use BEAR\Resource\RenderInterface;
use BEAR\Resource\ResourceObject;
use Ray\Di\Di\Named;

abstract class AuthBaseResourceObject extends ResourceObject
{
    public function __construct(#[Named('error_page')] private readonly RenderInterface $errorRenderer)
    {
    }

    public function setRendererForError(): void
    {
        $this->setRenderer($this->errorRenderer);
    }

    /**
     * @deprecated デバッグ用なので、プロダクトコードでは使用しないこと
     */
    public function setDebugMessage(string $msg): static
    {
        $this->body['debug'] = $msg;

        return $this;
    }
}
