<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\Injector;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private ResourceInterface $resource;

    public function setUp(): void
    {
        $injector = Injector::getInstance('app');
        $this->resource = $injector->getInstance(ResourceInterface::class);
    }

    public function testOnPost(): void
    {
        // 準備
        $ro = $this->resource->post('page://self/login');

        // 検証
        $this->assertSame(200, $ro->code);
    }
}
