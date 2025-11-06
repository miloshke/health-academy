<?php

namespace App\Modules\Package\Controllers;

use App\Library\Controller;
use App\Modules\Package\Repositories\PackageRepository;
use App\Modules\Package\Requests\StorePackageRequest;
use App\Modules\Package\Requests\UpdatePackageRequest;
use App\Modules\Package\Resources\PackageCollection;
use App\Modules\Package\Resources\PackageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(readonly private PackageRepository $packageRepository) {}

    /**
     * Display a listing of packages.
     */
    public function index(Request $request): PackageCollection
    {
        // TODO: Add policy check
        // $this->authorize('viewAny', Package::class);

        $perPage = $request->get('per_page', 10);
        $gymId = $request->get('gym_id');
        $packages = $this->packageRepository->getAll($perPage, $gymId);

        return new PackageCollection($packages);
    }

    /**
     * Store a newly created package.
     */
    public function store(StorePackageRequest $request): JsonResponse
    {
        $package = $this->packageRepository->create($request->validated());

        return (new PackageResource($package))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified package.
     */
    public function show(int $id): PackageResource
    {
        // TODO: Add policy check
        // $this->authorize('view', $package);

        $package = $this->packageRepository->find($id);

        if (!$package) {
            abort(404, 'Package not found');
        }

        return new PackageResource($package);
    }

    /**
     * Update the specified package.
     */
    public function update(UpdatePackageRequest $request, int $id): PackageResource
    {
        $package = $this->packageRepository->update($id, $request->validated());

        return new PackageResource($package);
    }

    /**
     * Remove the specified package.
     */
    public function destroy(int $id): JsonResponse
    {
        // TODO: Add policy check
        // $this->authorize('delete', $package);

        $deleted = $this->packageRepository->delete($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Package deleted successfully' : 'Failed to delete package',
        ]);
    }
}
