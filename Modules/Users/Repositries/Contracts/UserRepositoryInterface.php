<?php

namespace Modules\Users\Repositries\Contracts;

use Illuminate\Support\Collection;
use Modules\Products\app\Domain\Product;
use Modules\Users\Models\User;
interface UserRepositoryInterface
{
    public function UserExists(int $userId): bool;
    public function getUUID(int $userId): ?string;
    public function findByUUId(int $uuid): ?User;
    public function getRole(int $userId): array;
    public function findByEmail(string $email): ?User;
    
    public function create(array $data): User;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
}