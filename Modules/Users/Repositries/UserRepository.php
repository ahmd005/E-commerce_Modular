<?php 
namespace Modules\Users\Repositries;

use Modules\Users\Models\User;
use Modules\Users\Repositries\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function UserExists(int $userId): bool
    {
        return User::where('id', $userId)->exists();
    }
    public function getUUID(int $userId): ?string
    {
        return User::where('id', $userId)->value('uuid');
    }
    public function getRole(int $userId): array
    {
        $user = User::find($userId);
        return $user ? [$user->role] : [];
    }
    public function findByUUId(int $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }
    

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    

    public function create(array $data) : User
    {
        return User::create($data);
    }   
    public function update(int $id, array $data) : bool
    {
        $user = User::find($id);
        $user->update($data);
        return true;

    }
    public function delete(int $id) : bool
    {
        $user = User::find($id);
        return $user->delete();
    } 
}
