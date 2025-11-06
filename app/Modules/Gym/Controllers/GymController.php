<?php

namespace App\Modules\Gym\Controllers;

use App\Library\Controller;
use App\Modules\Gym\Repositories\GymRepository;
use App\Modules\Gym\Requests\StoreGymRequest;
use App\Modules\Gym\Requests\UpdateGymRequest;
use App\Modules\Gym\Resources\GymCollection;
use App\Modules\Gym\Resources\GymResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function __construct(readonly private GymRepository $gymRepository) {}

    /**
     * Display a listing of gyms.
     */
    public function index(Request $request): GymCollection
    {
        // TODO: Add policy check - SuperAdmin only
        // $this->authorize('viewAny', Gym::class);

        $perPage = $request->get('per_page', 10);
        $gyms = $this->gymRepository->getAll($perPage);

        return new GymCollection($gyms);
    }

    /**
     * Store a newly created gym.
     */
    public function store(StoreGymRequest $request): JsonResponse
    {
        $gym = $this->gymRepository->create($request->validated());

        return (new GymResource($gym))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified gym.
     */
    public function show(int $id): GymResource
    {
        // TODO: Add policy check
        // $this->authorize('view', $gym);

        $gym = $this->gymRepository->find($id);

        if (!$gym) {
            abort(404, 'Gym not found');
        }

        return new GymResource($gym);
    }

    /**
     * Update the specified gym.
     */
    public function update(UpdateGymRequest $request, int $id): GymResource
    {
        $gym = $this->gymRepository->update($id, $request->validated());

        return new GymResource($gym);
    }

    /**
     * Remove the specified gym.
     */
    public function destroy(int $id): JsonResponse
    {
        // TODO: Add policy check
        // $this->authorize('delete', $gym);

        $deleted = $this->gymRepository->delete($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Gym deleted successfully' : 'Failed to delete gym',
        ]);
    }
}
