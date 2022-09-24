<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Infrastructure\Authentication\FakeSession;
use Ray\Di\AbstractModule;

/**
 * 正常系のワークフローテストのために、認証機能の束縛をフェイクで上書きするためのモジュール
 * test-html-appコンテキストを呼ぶことで、HtmlModuleより優先されることを期待している
 */
class TestModule extends AbstractModule
{
    protected function configure(): void
    {
        $this->override(
            new class () extends AbstractModule {
                protected function configure(): void
                {
                    $this->bind(SessionHandlerInterface::class)->to(FakeSession::class);
                }
            }
        );
    }
}
