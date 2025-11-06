<?php

namespace App\Modules\User\Controllers\Admin;

use App\Library\Controller;
use App\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Resources\UserCollection;
use App\Modules\User\Resources\UserFullInfoResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function __construct(readonly private UserRepository $userRepository) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): UserCollection
    {
        if ($request->user()->role !== User::ROLE_ADMIN) {
            throw new AuthorizationException('Unauthorized');
        }

        $usersCollection = $this->userRepository->getAll();

        return new UserCollection($usersCollection);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): UserFullInfoResource
    {
        if ($request->user()->role !== User::ROLE_ADMIN) {
            throw new AuthorizationException('Unauthorized');
        }

        $user = $this->userRepository->create($request->validated());

        return new UserFullInfoResource($user);
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, int $id): UserFullInfoResource
    {
        if ($request->user()->role !== User::ROLE_ADMIN) {
            throw new AuthorizationException('Unauthorized');
        }

        $user = $this->userRepository->find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        return new UserFullInfoResource($user);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, int $id): UserFullInfoResource
    {
        if ($request->user()->role !== User::ROLE_ADMIN) {
            throw new AuthorizationException('Unauthorized');
        }

        $user = $this->userRepository->update($id, $request->validated());

        return new UserFullInfoResource($user);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        if ($request->user()->role !== User::ROLE_ADMIN) {
            throw new AuthorizationException('Unauthorized');
        }

        $deleted = $this->userRepository->delete($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'User deleted successfully' : 'Failed to delete user',
        ]);
    }
}
