<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\TestUtil;

use Ray\Di\AbstractModule;

class OverrideModule extends AbstractModule
{
    /** @var array<string, object> */
    private static array $binds = [];

    public static function addOrOverrideBind(string $interface, object $bindTo): void
    {
        self::$binds[$interface] = $bindTo;
    }

    public static function cleanBinds(): void
    {
        self::$binds = [];
    }

    protected function configure(): void
    {
        // テストケースごとに addOrOverrideBind() でセットした「差し替えたい束縛」を、ここで上書きする
        foreach (self::$binds as $interface => $instance) {
            $this->bind($interface)->toInstance($instance);
        }
    }
}
