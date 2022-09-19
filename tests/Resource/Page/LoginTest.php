<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Injector;
use DateTime;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\InjectorInterface;

class LoginTest extends TestCase
{
    private InjectorInterface $injector;
    private string $linkKey = 'next';
    private string $expectedRedirectTo;

    public function setUp(): void
    {
        $stubSession = $this->createStub(SessionHandlerInterface::class);
        $this->injector = Injector::getOverrideInstance('html-app', new class ($stubSession) extends AbstractModule{
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

        $now = new DateTime();
        $expectedQueryStr =
            'year=' . $now->format('Y')     // 年。4 桁の数字。
            . '&month=' . $now->format('n')   // 月。数字。先頭にゼロをつけない。
            . '&day=' . $now->format('j');    // 日。先頭にゼロをつけない。
        $this->expectedRedirectTo = "/{$this->linkKey}?" . $expectedQueryStr;
    }

    /**
     * @return array<string, mixed>
     *
     * @phpstan-ignore-next-line
     */
    private function dataForOnPostHtml(): array
    {
        return [
            'ユーザー名:hogetest' => ['username' => 'hogetest', 'password' => 'Fuga.1234'],
            'ユーザー名:piyotest' => ['username' => 'piyotest', 'password' => 'Fuga.1234'],
        ];
    }

    /**
     * @dataProvider dataForOnPostHtml
     */
    public function testOnPostHtml(string $username, string $password): void
    {
        // 準備
        $resource = $this->injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->post('page://self/login', ['username' => $username, 'password' => $password]);

        // 検証
        $this->assertSame(Code::SEE_OTHER, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $metas = $dom->getElementsByTagName('meta');
        $this->assertTrue((bool) $metas->count());
        foreach ($metas as $meta) {
            $content = $meta->getAttribute('content');
            if (! $content) {
                continue;
            }

            $this->assertStringContainsString($this->expectedRedirectTo, $content, 'METAタグのリダイレクト先が期待値と異なります');
        }

        $aTag = $dom->getElementById('redirect-to');
        $this->assertNotNull($aTag, 'NextページへジャンプするAタグの記述がありません');
        $this->assertSame($this->expectedRedirectTo, $aTag->getAttribute('href'), 'リンク先が期待値と異なります');
    }

    /**
     * @return array<string, mixed>
     *
     * @phpstan-ignore-next-line
     */
    private function dataForOnPostCaseUnauthorized(): array
    {
        return [
            'パスワード不一致' => ['username' => 'hogetest', 'password' => 'hogehoge'],
            '存在しないユーザー' => ['username' => 'piyopiyo', 'password' => 'Fuga.1234'],
        ];
    }

    /**
     * @dataProvider dataForOnPostCaseUnauthorized
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function testOnPostCaseUnauthorized(string $username, string $password): void
    {
        // 準備
        $expectedRedirectTo = '/index';
        $resource = $this->injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->post('page://self/login', ['username' => $username, 'password' => $password]);

        // 検証
        $this->assertSame(Code::SEE_OTHER, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $metas = $dom->getElementsByTagName('meta');
        $this->assertTrue((bool) $metas->count());
        foreach ($metas as $meta) {
            $content = $meta->getAttribute('content');
            if (! $content) {
                continue;
            }

            $this->assertStringContainsString($expectedRedirectTo, $content, 'METAタグのリダイレクト先が期待値と異なります');
        }

        $aTag = $dom->getElementById('redirect-to');
        $this->assertNotNull($aTag, 'IndexページへジャンプするAタグの記述がありません');
        $this->assertSame($expectedRedirectTo, $aTag->getAttribute('href'), 'リンク先が期待値と異なります');
    }
}
