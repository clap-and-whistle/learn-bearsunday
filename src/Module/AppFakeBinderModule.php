<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use BEAR\Resource\JsonRenderer;
use BEAR\Resource\RenderInterface;
use Cw\LearnBear\AppSpi\IdentityRepositoryInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Infrastructure\Authentication\FakeIdentityRepository;
use Cw\LearnBear\Infrastructure\Authentication\FakeSession;
use Ray\Di\AbstractModule;

class AppFakeBinderModule extends AbstractModule
{
    protected function configure(): void
    {
        // appコンテキストにおいては、Pageリソースの TwigErrorPageModule への依存を標準レンダラーでフェイクする
        $this->bind(RenderInterface::class)->annotatedWith('error_page')->to(JsonRenderer::class);

        // appコンテキストにおいては、Pageリソースが「認証機能を備えていない」のと同じ振る舞いをするように、下記２つをフェイクで置き換える
        $this->bind(SessionHandlerInterface::class)->to(FakeSession::class);
        $this->bind(IdentityRepositoryInterface::class)->to(FakeIdentityRepository::class);
    }
}
