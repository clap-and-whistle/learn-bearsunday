<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Authentication;

use Cw\LearnBear\AppSpi\IdentityRepositoryInterface;

use function password_hash;
use function password_verify;

use const PASSWORD_BCRYPT;

class IdentityRepository implements IdentityRepositoryInterface
{
    /** @var array<string, mixed>  */
    private readonly array $dummyStorage;

    public function __construct()
    {
        $this->dummyStorage = [
            'hogetest' => [
                'uuid' => 'ea210d8c-25b9-4f4a-b36a-a42634a9ab5c',
                'password' => password_hash('Fuga.1234', PASSWORD_BCRYPT),
            ],
            'piyotest' => [
                'uuid' => 'd31ebeeb-6825-4b0e-9dee-248b7ace9ffa',
                'password' => password_hash('Fuga.1234', PASSWORD_BCRYPT),
            ],
        ];
    }

    public function findByUserNameAndPassword(string $username, string $password): ?string
    {
        $identity = $this->dummyStorage[$username] ?? null;
        if ($identity === null) {
            return null;
        }

        return password_verify($password, $identity['password'])
            ? $identity['uuid']
            : null;
    }
}
