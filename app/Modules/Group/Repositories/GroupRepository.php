<?php

namespace App\Modules\Group\Repositories;

use App\Models\Group;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GroupRepository
{
    public function getAll(?int $perPage = 10, ?int $gymId = null): LengthAwarePaginator
    {
        $query = Group::with(['gym', 'locations']);

        if ($gymId) {
            $query->where('gym_id', $gymId);
        }

        return $query->withCount(['users', 'locations'])->paginate($perPage);
    }

    public function find(int $id): ?Group
    {
        return Group::with(['gym', 'locations', 'users'])->find($id);
    }

    public function create(array $data): Group
    {
        $locationIds = $data['location_ids'] ?? [];
        unset($data['location_ids']);

        $group = Group::create($data);

        if (!empty($locationIds)) {
            $group->locations()->attach($locationIds);
        }

        return $group->fresh(['gym', 'locations']);
    }

    public function update(int $id, array $data): Group
    {
        $group = Group::findOrFail($id);

        $locationIds = $data['location_ids'] ?? null;
        unset($data['location_ids']);

        $group->update($data);

        if ($locationIds !== null) {
            $group->locations()->sync($locationIds);
        }

        return $group->fresh(['gym', 'locations']);
    }

    public function delete(int $id): bool
    {
        $group = Group::findOrFail($id);

        return $group->delete();
    }
}
