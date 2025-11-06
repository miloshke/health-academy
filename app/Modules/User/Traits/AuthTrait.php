<?php

namespace App\Modules\User\Traits;

use App\Models\User;
use App\Modules\User\Resources\UserResource;
use stdClass;

trait AuthTrait
{
    public function buildAuthResponse(User $user, string $token): array
    {
        $abilities = [];
        if ($user->role === User::ROLE_ADMIN) {
            $abilityAdmin = new stdClass();
            $abilityAdmin->action = 'manage';
            $abilityAdmin->subject = 'all';
            $abilities[] = $abilityAdmin;
        } else if ($user->role === User::ROLE_USER) {
            $abilityUser = new stdClass();
            $abilityUser->action = 'read';
            $abilityUser->subject = 'user';
            $abilityAuth = new stdClass();
            $abilityAuth->action = 'read';
            $abilityAuth->subject = 'auth';
            $abilities[] = $abilityUser;
            $abilities[] = $abilityAuth;
        }

        return [
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'userData' => new UserResource($user),
            'userAbilities' => $abilities
        ];
    }
}
