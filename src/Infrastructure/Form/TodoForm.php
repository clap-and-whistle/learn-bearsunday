<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Form;

use Aura\Html\Helper\Tag;
use Ray\WebFormModule\AbstractForm;

use function assert;

use const PHP_EOL;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TodoForm extends AbstractForm
{
    use QueryForNext;

    /**
     * @psalm-suppress TooManyArguments
     */
    public function init(): void
    {
        $this->setField('title')
            ->setAttribs([
                'id' => 'todo[title]',
                'name' => 'todo[title]',
                'class' => 'form-control',
                'size' => 20,
            ]);

        $this->setField('submit', 'submit')
            ->setAttribs([
                'name' => 'submit',
                'value' => '登録',
                'class' => 'btn btn-primary',
            ]);

        // validationの設定
        $this->filter->validate('title')->is('strlenMin', 1);
        $this->filter->useFieldMessage('title', '必ず入力してください');
    }

    public function __toString(): string
    {
        // nextページを呼ぶ際に必要となるクエリ文字列（Next::onGet()の引数に相当）を準備
        $form = $this->form([
            'method' => 'post',
            'action' => '/next' . $this->getQueryStrForNext(),
        ]);

        $tag = $this->helper->get('tag');
        assert($tag instanceof Tag);
        $form .= $tag('div', ['class' => 'form-group']);
        $form .= $this->input('title');
        $form .= $this->error('title');
        // @phpstan-ignore-next-line
        $form .= $this->helper->tag('/div') . PHP_EOL;

        // submit
        $form .= $this->input('submit');
        // @phpstan-ignore-next-line
        $form .= $this->helper->tag('/form');

        return $form;
    }
}
