<?php

declare(strict_types=1);

namespace Cw\LearnBear\BEAR\Package;

use BEAR\AppMeta\Meta;
use BEAR\Package\Module;
use BEAR\Sunday\Extension\Application\AppInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Injector as RayInjector;
use Ray\Di\InjectorInterface;

/**
 * @see \BEAR\Package\Injector\PackageInjector
 */
final class Injector
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function getOverrideInstance(string $appName, string $context, string $appDir, AbstractModule $overrideModule): InjectorInterface
    {
        $meta = new Meta($appName, $context, $appDir);
        $scriptDir = $meta->tmpDir . '/di';

        $module = (new Module())($meta, $context);
        $module->override($overrideModule);

        $injector = new RayInjector($module, $scriptDir);
        $injector->getInstance(AppInterface::class);

        return $injector;
    }
}
