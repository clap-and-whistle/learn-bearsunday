<?php

declare(strict_types=1);

namespace Cw\LearnBear\Module;

use Cw\LearnBear\Annotation\CheckAuth;
use Cw\LearnBear\AppSpi\IdentityRepositoryInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Infrastructure\Authentication\CwSession;
use Cw\LearnBear\Infrastructure\Authentication\IdentityRepository;
use Cw\LearnBear\Interceptor\AuthCheckInterceptor;
use Cw\LearnBear\Resource\AuthBaseResourceObject;
use Ray\Di\AbstractModule;

class CwAuthModule extends AbstractModule
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->bind(SessionHandlerInterface::class)->to(CwSession::class);
        $this->bind(IdentityRepositoryInterface::class)->to(IdentityRepository::class);

        $this->bindInterceptor(
            $this->matcher->subclassesOf(AuthBaseResourceObject::class),    // AuthBaseResourceObjectの継承または実装クラスの
            $this->matcher->annotatedWith(CheckAuth::class),    // @CheckAuthアノテーションがアノテートされているメソッドには
            [AuthCheckInterceptor::class],  // AuthCheckInterceptorインターセプターを束縛
        );
    }
}
