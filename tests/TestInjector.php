<?php

declare(strict_types=1);

namespace Cw\LearnBear;

use BEAR\Package\Injector as BearInjector;
use Cw\LearnBear\BEAR\Package\Injector as TestPackageInjector;
use Ray\Di\AbstractModule;
use Ray\Di\InjectorInterface;

use function dirname;

class TestInjector
{
    /**
     * Serialized injector instances
     *
     * @var array<string, InjectorInterface>
     */
    private static array $originalInstances = [];

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function getInstance(string $context): InjectorInterface
    {
        if (isset(self::$originalInstances[$context])) {
            return self::$originalInstances[$context];
        }

        self::$originalInstances[$context] = BearInjector::getInstance(__NAMESPACE__, $context, dirname(__DIR__));

        return self::$originalInstances[$context];
    }

    public static function getOverrideInstance(string $context, AbstractModule $overrideModule): InjectorInterface
    {
        return TestPackageInjector::getOverrideInstance(__NAMESPACE__, $context, dirname(__DIR__), $overrideModule);
    }
}
