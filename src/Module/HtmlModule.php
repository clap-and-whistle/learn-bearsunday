<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use Cw\LearnBear\Infrastructure\Form\TodoForm;
use Madapaja\TwigModule\TwigErrorPageModule;
use Madapaja\TwigModule\TwigModule;
use Ray\Di\AbstractModule;
use Ray\WebFormModule\AuraInputModule;
use Ray\WebFormModule\FormInterface;

class HtmlModule extends AbstractModule
{
    protected function configure(): void
    {
        $this->install(new TwigModule());
        $this->install(new TwigErrorPageModule());
        $this->install(new AuraInputModule());
        $this->bind(TodoForm::class);
        $this->bind(FormInterface::class)->annotatedWith('todo_form')->to(TodoForm::class);
    }
}
