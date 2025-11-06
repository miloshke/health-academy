<?php

namespace App\Modules\Location\Repositories;

use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationRepository
{
    public function getAll(?int $perPage = 10, ?int $gymId = null): LengthAwarePaginator
    {
        $query = Location::with('gym');

        if ($gymId) {
            $query->where('gym_id', $gymId);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Location
    {
        return Location::with(['gym', 'users', 'groups'])->find($id);
    }

    public function create(array $data): Location
    {
        return Location::create($data);
    }

    public function update(int $id, array $data): Location
    {
        $location = Location::findOrFail($id);
        $location->update($data);

        return $location->fresh();
    }

    public function delete(int $id): bool
    {
        $location = Location::findOrFail($id);

        return $location->delete();
    }
}
