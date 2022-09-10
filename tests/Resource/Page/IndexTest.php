<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\Injector;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private ResourceInterface $resource;
    private string $linkKey = 'login';
    private string $expectedLinkDestination;

    protected function setUp(): void
    {
        $injector = Injector::getInstance('app');
        $this->resource = $injector->getInstance(ResourceInterface::class);

        $this->expectedLinkDestination = "/{$this->linkKey}";
    }

    public function testOnGet(): void
    {
        // 実行
        $ro = $this->resource->get('page://self/index');

        // 検証
        $this->assertSame(Code::OK, $ro->code);
        $this->assertArrayHasKey($this->linkKey, $ro->body['_links']);
        $this->assertSame($this->expectedLinkDestination, $ro->body['_links'][$this->linkKey]['href']);
    }

    public function testOnGetHtml(): void
    {
        // 準備
        $injector = Injector::getInstance('html-app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/index');

        // 検証
        $this->assertSame(Code::OK, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $formElement = $dom->getElementById('login-form');
        $this->assertNotNull($formElement, 'ログインフォームがHTMLに記述されていません');
        $this->assertSame($this->expectedLinkDestination, $formElement->getAttribute('action'), 'フォームのアクション先URLが期待値と異なります');
    }
}
