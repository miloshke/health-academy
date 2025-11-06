<?php

namespace App\Modules\Package\Repositories;

use App\Models\Package;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PackageRepository
{
    public function getAll(?int $perPage = 10, ?int $gymId = null): LengthAwarePaginator
    {
        $query = Package::with('gym');

        if ($gymId) {
            $query->where('gym_id', $gymId);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Package
    {
        return Package::with(['gym', 'users'])->find($id);
    }

    public function create(array $data): Package
    {
        return Package::create($data);
    }

    public function update(int $id, array $data): Package
    {
        $package = Package::findOrFail($id);
        $package->update($data);

        return $package->fresh();
    }

    public function delete(int $id): bool
    {
        $package = Package::findOrFail($id);

        return $package->delete();
    }
}
