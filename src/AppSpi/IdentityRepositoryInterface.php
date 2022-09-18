<?php

declare(strict_types=1);

namespace Cw\LearnBear\AppSpi;

interface IdentityRepositoryInterface
{
    public function findByUserNameAndPassword(string $username, string $password): ?string;
}
