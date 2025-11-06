<?php

namespace App\Modules\Gym\Repositories;

use App\Models\Gym;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GymRepository
{
    public function __construct(readonly private Gym $gym) {}

    public function getAll(?int $perPage = 10): LengthAwarePaginator
    {
        return $this->gym->with(['locations', 'users'])->paginate($perPage);
    }

    public function find(int $id): ?Gym
    {
        return $this->gym->with(['locations', 'users', 'groups', 'packages'])->find($id);
    }

    public function findBySlug(string $slug): ?Gym
    {
        return $this->gym->where('slug', $slug)->first();
    }

    public function create(array $data): Gym
    {
        return $this->gym->create($data);
    }

    public function update(int $id, array $data): Gym
    {
        $gym = $this->gym->find($id);
        $gym->update($data);
        return $gym->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->gym->find($id)->delete();
    }

    public function getActive(): Collection
    {
        return $this->gym->where('status', 'active')->get();
    }
}
