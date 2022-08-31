<?php

declare(strict_types=1);

use Cw\LearnBear\Bootstrap;

require dirname(__DIR__) . '/autoload.php';
exit((new Bootstrap())(PHP_SAPI === 'cli' ? 'cli-html-app' : 'html-app', $GLOBALS, $_SERVER));
