<?php
namespace Modules\Users\app\Infrastructure;

use Modules\Users\app\Contracts\UserContract;
use Modules\Users\Models\User;
use Modules\Users\Repositries\Contracts\UserRepositoryInterface;
class UserService implements UserContract
{

   public function __construct(private UserRepositoryInterface $userRepository)
    {
    }


    public function userExists(int $userId): bool
    {
        return $this->userRepository->UserExists($userId);
    }

    public function getUserEmail(int $userId): ?string
    {
        return $this->userRepository->findByUUId($userId)?->email;
    }

    public function getUserUuid(int $userId): ?string
    {
        return $this->userRepository->getUUID($userId);
    }
    

    public function getUserRoles(int $userId): array
    {
        return $this->userRepository->getRole($userId);
    }
}