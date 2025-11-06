<?php

namespace App\Modules\Group\Controllers;

use App\Library\Controller;
use App\Modules\Group\Repositories\GroupRepository;
use App\Modules\Group\Requests\StoreGroupRequest;
use App\Modules\Group\Requests\UpdateGroupRequest;
use App\Modules\Group\Resources\GroupCollection;
use App\Modules\Group\Resources\GroupResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct(readonly private GroupRepository $groupRepository) {}

    /**
     * Display a listing of groups.
     */
    public function index(Request $request): GroupCollection
    {
        // TODO: Add policy check
        // $this->authorize('viewAny', Group::class);

        $perPage = $request->get('per_page', 10);
        $gymId = $request->get('gym_id');
        $groups = $this->groupRepository->getAll($perPage, $gymId);

        return new GroupCollection($groups);
    }

    /**
     * Store a newly created group.
     */
    public function store(StoreGroupRequest $request): JsonResponse
    {
        $group = $this->groupRepository->create($request->validated());

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified group.
     */
    public function show(int $id): GroupResource
    {
        // TODO: Add policy check
        // $this->authorize('view', $group);

        $group = $this->groupRepository->find($id);

        if (!$group) {
            abort(404, 'Group not found');
        }

        return new GroupResource($group);
    }

    /**
     * Update the specified group.
     */
    public function update(UpdateGroupRequest $request, int $id): GroupResource
    {
        $group = $this->groupRepository->update($id, $request->validated());

        return new GroupResource($group);
    }

    /**
     * Remove the specified group.
     */
    public function destroy(int $id): JsonResponse
    {
        // TODO: Add policy check
        // $this->authorize('delete', $group);

        $deleted = $this->groupRepository->delete($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Group deleted successfully' : 'Failed to delete group',
        ]);
    }
}
