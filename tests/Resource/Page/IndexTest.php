<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Resource\TestUtil\OverrideModule;
use Cw\LearnBear\TestInjector;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private string $linkKey = 'login';
    private string $expectedLinkDestination;

    protected function setUp(): void
    {
        $this->expectedLinkDestination = "/{$this->linkKey}";
    }

    protected function tearDown(): void
    {
        OverrideModule::cleanBinds();
        parent::tearDown();
    }

    public function testOnGetHtml(): void
    {
        // 準備
        $injector = TestInjector::getInstance('html-app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/index');

        // 検証
        $this->assertSame(Code::OK, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $this->assertNull($dom->getElementById('flash-message'), 'flash-message: セットされないシーンです');
        $formElement = $dom->getElementById('login-form');
        $this->assertNotNull($formElement, 'ログインフォームがHTMLに記述されていません');
        $this->assertSame($this->expectedLinkDestination, $formElement->getAttribute('action'), 'フォームのアクション先URLが期待値と異なります');
    }

    public function testOnGetHtmlWithFlashMessage(): void
    {
        // 準備
        $expectedMessage = 'HTMLテストメッセージ';
        $sessionHandlerStub = $this->createStub(SessionHandlerInterface::class);
        $sessionHandlerStub->method('getFlashMessage')->willReturn($expectedMessage);
        OverrideModule::addOrOverrideBind(SessionHandlerInterface::class, $sessionHandlerStub);
        $injector = TestInjector::getOverrideInstance('html-app', new OverrideModule());
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/index');

        // 検証
        $this->assertSame(Code::OK, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $flashMessageElement = $dom->getElementById('flash-message');
        $this->assertNotNull($flashMessageElement, 'flash-message: セットされていません');
        $this->assertSame($expectedMessage, $flashMessageElement->textContent, 'flash-message: 期待値と異なっています');
        $formElement = $dom->getElementById('login-form');
        $this->assertNotNull($formElement, 'ログインフォームがHTMLに記述されていません');
        $this->assertSame($this->expectedLinkDestination, $formElement->getAttribute('action'), 'フォームのアクション先URLが期待値と異なります');
    }
}
