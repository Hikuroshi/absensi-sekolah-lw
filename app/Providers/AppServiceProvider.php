<?php

namespace App\Providers;

use App\Enums\UserRole;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user->role === UserRole::ADMIN;
        });

        Gate::define('isGuru', function ($user) {
            return $user->role === UserRole::GURU || $user->role === UserRole::WALI_KELAS;
        });

        Gate::define('isSiswa', function ($user) {
            return $user->role === UserRole::SISWA;
        });

        Gate::define('isWaliKelas', function ($user) {
            return $user->role === UserRole::WALI_KELAS;
        });

        Gate::define('isKetuaKelas', function ($user) {
            return $user->role === UserRole::KETUA_KELAS;
        });
    }
}
