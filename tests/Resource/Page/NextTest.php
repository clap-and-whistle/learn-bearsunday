<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Injector;
use Cw\LearnBear\Interceptor\AuthCheckInterceptor;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;

use function json_decode;

class NextTest extends TestCase
{
    private string $linkKey = 'logout';
    private string $expectedLinkDestination;

    protected function setUp(): void
    {
        Injector::getInstance('html-app')->getInstance(SessionHandlerInterface::class)->destroy();
        $this->expectedLinkDestination = "/{$this->linkKey}";
    }

    public function testOnGetApp(): void
    {
        // 準備
        $injector = Injector::getInstance('app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/next', ['year' => 2001, 'month' => 1, 'day' => 1]);

        // 検証
        $this->assertSame(Code::OK, $ro->code);
        $json = json_decode((string) $ro);
        $this->assertObjectHasAttribute('_links', $json);
        $this->assertSame('/' . $this->linkKey, $json->_links->{$this->linkKey}->href);
    }

    public function testOnGetHtml(): void
    {
        // 準備
        $stubSession = $this->createStub(SessionHandlerInterface::class);
        $stubSession->method('isNotAuthorized')->willReturn(false);
        $injector = Injector::getOverrideInstance('html-app', new class ($stubSession) extends AbstractModule {
            public function __construct(
                private readonly SessionHandlerInterface $sessionHandlerStub
            ) {
                parent::__construct();
            }

            protected function configure(): void
            {
                $this->bind(SessionHandlerInterface::class)->toInstance($this->sessionHandlerStub);
            }
        });

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

    public function testOnGetHtmlCaseUnauthorized(): void
    {
        // 準備
        $injector = Injector::getInstance('html-app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/next', ['year' => 2001, 'month' => 1, 'day' => 1]);

        // 検証
        $this->assertSame(Code::UNAUTHORIZED, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);
        $this->assertStringContainsString(AuthCheckInterceptor::AUTH_ERROR_MESSAGE, $htmlContents);
    }
}
