<?php

declare(strict_types=1);

namespace Cw\LearnBear\Http;

use BEAR\Dev\Http\HttpResource;
use BEAR\Resource\Code;
use Cw\LearnBear\Hypermedia\WorkflowTest as Workflow;
use DateTime;

class WorkflowTest extends Workflow
{
    protected function setUp(): void
    {
        $this->resource = new HttpResource('127.0.0.1:8080', __DIR__ . '/index.php', __DIR__ . '/log/workflow.log');
    }

    /**
     * @depends testIndex
     */
    public function testLoginAllow(string $requestPath): string
    {
        // 準備
        $inputUsername = 'hogetest';
        $inputPassword = 'Fuga.1234';

        // 実行
        $loginRo = $this->resource->post($requestPath, ['username' => $inputUsername, 'password' => $inputPassword]);

        // 検証
        // note: Loginリソースの結果ではなく、リダイレクトまで済んだ結果(つまりNextリソースの結果が返されている)
        $this->assertSame(Code::OK, $loginRo->code);

        // note: そのため、継承元Workflowにて続くテストケースへ引き渡す string $requestPath を手動生成して返さねばならない
        $now = new DateTime();

        return '/next'
            . '?year=' . $now->format('Y')
            . '&month=' . $now->format('n')
            . '&day=' . $now->format('j');
    }
}
