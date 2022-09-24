<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Authentication;

use Cw\LearnBear\AppSpi\IdentityRepositoryInterface;

class FakeIdentityRepository implements IdentityRepositoryInterface
{
    /**
     * 照合結果が必ず「照合成功」を示すようにメソッドを設定
     */
    public function findByUserNameAndPassword(string $username, string $password): ?string
    {
        return 'pass_unconditionally';
    }
}
