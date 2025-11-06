<?php

namespace App\Modules\User\Controllers;

use App\Library\Controller;
use App\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Requests\ForgotPasswordRequest;
use App\Modules\User\Requests\ResetPasswordRequest;
use App\Modules\User\Traits\AuthTrait;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

final class AuthController extends Controller
{
    use AuthTrait;

    public function __construct(private readonly UserRepository $userRepository) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $credentials = request(['email', 'password']);
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        /** @var User $user */
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email not verified.',
            ], 403);
        }

        // TODO: Distinct between mobile and web tokens
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        $user->last_login = Carbon::now();
        $user->save();

        return response()->json($this->buildAuthResponse($user, $token));
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
            'confirmed_password' => 'required|same:password',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'role' => User::ROLE_USER,
            'password' => bcrypt($request->password),
        ]);

        if ($user->save()) {
            event(new Registered($user));

            return response()->json(['message' => __('email_sent')], 201);
        }

        return response()->json(['error' => 'Provide proper details'], 400);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = $this->userRepository->findByEmail($request->email);

        if ($user === null) {
            return response()->json(['message' => 'User not found'], 400);
        }

        if ($user->email_verified_at !== null) {
            return response()->json(['message' => 'User already verified'], 400);
        }

        if (Cache::get('email_verification_sent_'.$user->email) !== null) {
            return response()->json(['message' => 'Verification already resent'], 400);
        }

        Cache::set('email_verification_sent_'.$user->email, now()->addHour());
        event(new Registered($user));

        return response()->json(['message' => 'Email verification sent']);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->email);

        if ($user === null) {
            return response()->json(['message' => 'Reset password email sent']);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset password email sent'])
            : response()->json(['message' => 'Something went wrong'], 400);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            static function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ]);

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password successfully updated'])
            : response()->json(['message' => 'Something went wrong'], 400);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
