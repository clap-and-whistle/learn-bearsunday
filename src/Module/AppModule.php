<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use BEAR\Dotenv\Dotenv;
use BEAR\Package\AbstractAppModule;
use BEAR\Package\PackageModule;
use Cw\LearnBear\AppSpi\LoggerInterface;
use Cw\LearnBear\Infrastructure\Logging\DebugLogger;
use Ray\AuraSqlModule\AuraSqlModule;

use function dirname;

class AppModule extends AbstractAppModule
{
    protected function configure(): void
    {
        (new Dotenv())->load(dirname(__DIR__, 2));
        $this->install(new PackageModule());
        $this->install(new CwAuthModule());
        $this->install(new HtmlModule());
        $appDir = $this->appMeta->appDir;
        $dbConfig = 'sqlite:' . $appDir . '/var/db/todo.sqlite3';
        $this->install(new AuraSqlModule($dbConfig));
        $this->bind(LoggerInterface::class)->to(DebugLogger::class);
    }
}
