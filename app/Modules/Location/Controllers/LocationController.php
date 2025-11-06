<?php

namespace App\Modules\Location\Controllers;

use App\Library\Controller;
use App\Modules\Location\Repositories\LocationRepository;
use App\Modules\Location\Requests\StoreLocationRequest;
use App\Modules\Location\Requests\UpdateLocationRequest;
use App\Modules\Location\Resources\LocationCollection;
use App\Modules\Location\Resources\LocationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(readonly private LocationRepository $locationRepository) {}

    /**
     * Display a listing of locations.
     */
    public function index(Request $request): LocationCollection
    {
        // TODO: Add policy check
        // $this->authorize('viewAny', Location::class);

        $perPage = $request->get('per_page', 10);
        $gymId = $request->get('gym_id');
        $locations = $this->locationRepository->getAll($perPage, $gymId);

        return new LocationCollection($locations);
    }

    /**
     * Store a newly created location.
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = $this->locationRepository->create($request->validated());

        return (new LocationResource($location))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified location.
     */
    public function show(int $id): LocationResource
    {
        // TODO: Add policy check
        // $this->authorize('view', $location);

        $location = $this->locationRepository->find($id);

        if (!$location) {
            abort(404, 'Location not found');
        }

        return new LocationResource($location);
    }

    /**
     * Update the specified location.
     */
    public function update(UpdateLocationRequest $request, int $id): LocationResource
    {
        $location = $this->locationRepository->update($id, $request->validated());

        return new LocationResource($location);
    }

    /**
     * Remove the specified location.
     */
    public function destroy(int $id): JsonResponse
    {
        // TODO: Add policy check
        // $this->authorize('delete', $location);

        $deleted = $this->locationRepository->delete($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Location deleted successfully' : 'Failed to delete location',
        ]);
    }
}
