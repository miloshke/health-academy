<?php

use App\Http\Controllers\PaymentController;
use App\Modules\Health\Controllers\HealthController;
use App\Modules\PDF\Controllers\PdfTemplateController;
use App\Modules\PDF\Models\Flyer;
use App\Modules\Gym\Controllers\GymController;
use App\Modules\Group\Controllers\GroupController;
use App\Modules\Location\Controllers\LocationController;
use App\Modules\Package\Controllers\PackageController;
use App\Modules\User\Controllers\Admin\UserAdminController;
use App\Modules\User\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect(env('APP_URL').'/verification/email-verified');
})->middleware(['signed'])->name('verification.verify');

if (env('APP_ENV') !== 'production') {
    Route::get('test', function () {
        return new Response([
            'test'
        ]);
    });
}

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('resend-verification', [AuthController::class, 'resendVerification'])->name('auth.resendVerification');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgotPassword');

    Route::get('/reset-password/{token}', function (string $token) {
        return redirect(env('APP_URL').'/reset-password/'.$token);
    })->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.resetPassword');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

// Test route without authentication (for development)
if (env('APP_ENV') !== 'production') {
    Route::prefix('admin/users')->name('admin.users.test.')->group(function () {
        Route::get('/test', function () {
            $users = \App\Models\User::paginate(10);
            return new \App\Modules\User\Resources\UserCollection($users);
        })->name('test');
    });
}

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    if (env('APP_ENV') !== 'production') {
        Route::get('test-auth', function () {
            return response()->json(['message' => 'Authenticated']);
        });
    }

    // User Profile Routes
    // TODO: Implement UserProfileController
    // Route::prefix('user')->name('user.')->group(function () {
    //     Route::get('profile', [\App\Http\Controllers\UserProfileController::class, 'show']);
    //     Route::put('profile', [\App\Http\Controllers\UserProfileController::class, 'updateProfile']);
    //     Route::put('password', [\App\Http\Controllers\UserProfileController::class, 'updatePassword']);
    //     Route::get('subscription', [\App\Http\Controllers\UserProfileController::class, 'getSubscription']);
    // });

    // Admin User Management Routes
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserAdminController::class, 'index'])->name('index');
        Route::post('/', [UserAdminController::class, 'store'])->name('store');
        Route::get('/{user}', [UserAdminController::class, 'show'])->name('show');
        Route::put('/{user}', [UserAdminController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserAdminController::class, 'destroy'])->name('destroy');
    });

    // Gym Management Routes
    Route::prefix('gyms')->name('gyms.')->group(function () {
        Route::get('/', [GymController::class, 'index'])->name('index');
        Route::post('/', [GymController::class, 'store'])->name('store');
        Route::get('/{gym}', [GymController::class, 'show'])->name('show');
        Route::put('/{gym}', [GymController::class, 'update'])->name('update');
        Route::delete('/{gym}', [GymController::class, 'destroy'])->name('destroy');
    });

    // Location Management Routes
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::get('/{location}', [LocationController::class, 'show'])->name('show');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
    });

    // Group Management Routes
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::post('/', [GroupController::class, 'store'])->name('store');
        Route::get('/{group}', [GroupController::class, 'show'])->name('show');
        Route::put('/{group}', [GroupController::class, 'update'])->name('update');
        Route::delete('/{group}', [GroupController::class, 'destroy'])->name('destroy');
    });

    // Package Management Routes
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('index');
        Route::post('/', [PackageController::class, 'store'])->name('store');
        Route::get('/{package}', [PackageController::class, 'show'])->name('show');
        Route::put('/{package}', [PackageController::class, 'update'])->name('update');
        Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy');
    });
});

Route::get('health', HealthController::class);
