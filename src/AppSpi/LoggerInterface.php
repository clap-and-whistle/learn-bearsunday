<?php

declare(strict_types=1);

namespace Cw\LearnBear\AppSpi;

interface LoggerInterface
{
    public function log(string $message): void;
}
