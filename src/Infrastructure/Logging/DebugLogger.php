<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Logging;

use BEAR\AppMeta\AbstractAppMeta;
use Cw\LearnBear\AppSpi\LoggerInterface;
use DateTime;

use function error_log;

use const PHP_EOL;

class DebugLogger implements LoggerInterface
{
    private readonly string $logFilePath;

    public function __construct(AbstractAppMeta $meta)
    {
        $this->logFilePath = $meta->logDir . '/debug.log';
    }

    public function log(string $message): void
    {
        $now = new DateTime();
        error_log($now->format('Y-m-d H:i:s') . ' ' . $message . PHP_EOL, 3, $this->logFilePath);
    }
}
