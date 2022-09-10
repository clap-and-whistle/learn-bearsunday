<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceObject;
use Cw\LearnBear\AppSpi\LoggerInterface;

use function password_hash;

use const PASSWORD_BCRYPT;

class Login extends ResourceObject
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onPost(string $username = '', string $password = ''): static
    {
        $this->logger->log('username: ' . $username . ', password: ' . password_hash($password, PASSWORD_BCRYPT));

        return $this;
    }
}
