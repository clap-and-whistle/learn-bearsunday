<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use Madapaja\TwigModule\TwigErrorPageModule;
use Madapaja\TwigModule\TwigModule;
use Ray\Di\AbstractModule;

class HtmlModule extends AbstractModule
{
    protected function configure(): void
    {
        $this->install(new TwigModule());
        $this->install(new TwigErrorPageModule());
        $this->override(new CwAuthModule());
    }
}
