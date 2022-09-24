<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use Cw\LearnBear\Infrastructure\Form\TodoForm;
use Ray\Di\AbstractModule;
use Ray\WebFormModule\FormInterface;

class FormModule extends AbstractModule
{
    protected function configure(): void
    {
        $this->bind(TodoForm::class);
        $this->bind(FormInterface::class)->annotatedWith('todo_form')->to(TodoForm::class);
    }
}
