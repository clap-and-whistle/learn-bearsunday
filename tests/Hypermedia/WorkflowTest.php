<?php

declare(strict_types=1);

namespace Cw\LearnBear\Hypermedia;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use Cw\LearnBear\Injector;
use PHPUnit\Framework\TestCase;
use Ray\Di\InjectorInterface;

use function json_decode;

class WorkflowTest extends TestCase
{
    protected ResourceInterface $resource;
    protected InjectorInterface $injector;

    protected function setUp(): void
    {
        $this->injector = Injector::getInstance('app');
        $this->resource = $this->injector->getInstance(ResourceInterface::class);
    }

    public function testIndex(): string
    {
        // 実行
        $indexRo = $this->resource->get('/');

        // 検証
        $this->assertSame(Code::OK, $indexRo->code);

        return json_decode((string) $indexRo)->_links->login->href;
    }

    /**
     * @depends testIndex
     */
    public function testLoginAllow(string $requestPath): string
    {
        // 準備
        $inputUsername = 'hogetest';
        $inputPassword = 'Fuga1234';

        // 実行
        $loginRo = $this->resource->post($requestPath, ['username' => $inputUsername, 'password' => $inputPassword]);

        // 検証
        $this->assertSame(Code::SEE_OTHER, $loginRo->code);

        return json_decode((string) $loginRo)->_links->redirect->href;
    }

    /**
     * @depends testLoginAllow
     */
    public function testNext(string $requestPath): string
    {
        // 実行
        $nextRo = $this->resource->get($requestPath);

        // 検証
        $this->assertSame(Code::OK, $nextRo->code);

        return json_decode((string) $nextRo)->_links->logout->href;
    }

    /**
     * @depends testNext
     */
    public function testLogout(string $requestPath): string
    {
        // 実行
        $logoutRo = $this->resource->get($requestPath);

        // 検証
        $this->assertSame(Code::OK, $logoutRo->code);

        return json_decode((string) $logoutRo)->_links->index->href;
    }

    /**
     * @depends testLogout
     */
    public function testReturnIndex(string $requestPath): ResourceObject
    {
        // 実行
        $ro = $this->resource->get($requestPath);

        // 検証
        $this->assertSame(Code::OK, $ro->code);

        return $ro;
    }
}
