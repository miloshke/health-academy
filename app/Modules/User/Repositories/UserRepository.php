<?php

namespace App\Modules\User\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function __construct(readonly private User $user) {}

    public function getAll(): LengthAwarePaginator
    {
        return $this->user->paginate(10);
    }

    public function find(int $id): ?User
    {
        return $this->user->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    public function findByIds(array $ids): Collection
    {
        return $this->user->whereIn('id', $ids)->get();
    }

    public function create(array $data): User
    {
        return $this->user->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->user->find($id);
        $user->update($data);
        return $user->fresh();
    }

    public function removeAllUserTokens(int $userId): void
    {
        $this->user->find($userId)->tokens()->delete();
    }

    public function delete(int $userId): bool
    {
        return $this->user->find($userId)->delete();
    }
}
