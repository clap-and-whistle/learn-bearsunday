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

class NextTest extends TestCase
{
    private SessionHandlerInterface $stubSession;
    private string $linkKey = 'logout';
    private string $expectedLinkDestination;

    protected function setUp(): void
    {
        $this->stubSession = $this->createStub(SessionHandlerInterface::class);
        $this->expectedLinkDestination = "/{$this->linkKey}";
    }

    protected function tearDown(): void
    {
        OverrideModule::cleanBinds();
        parent::tearDown();
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     */
    public function testOnGetHtml(): void
    {
        // 準備
        // @phpstan-ignore-next-line
        $this->stubSession->method('isNotAuthorized')->willReturn(false);
        OverrideModule::addOrOverrideBind(SessionHandlerInterface::class, $this->stubSession);
        $injector = TestInjector::getOverrideInstance('html-app', new OverrideModule());
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/next', ['year' => 2001, 'month' => 1, 'day' => 1]);

        // 検証
        $this->assertSame(Code::OK, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $element = $dom->getElementById('link_' . $this->linkKey);
        $this->assertNotNull($element, 'リンクがHTMLに記述されていません');
        $this->assertSame('a', $element->tagName, 'LogoutページへジャンプするAタグの記述がありません');
        $this->assertSame('ログアウトする', $element->nodeValue, 'Aタグの表示文字列が期待値と異なります');
        $this->assertSame($this->expectedLinkDestination, $element->getAttribute('href'), 'リンク先が期待値と異なります');
    }

    /**
     * @noinspection NonAsciiCharacters
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function testOnGetHtml_未認証(): void
    {
        // 準備
        $injector = TestInjector::getInstance('html-app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/next', ['year' => 2001, 'month' => 1, 'day' => 1]);

        // 検証
        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);

        $this->assertSame(Code::UNAUTHORIZED, $ro->code);
    }
}
