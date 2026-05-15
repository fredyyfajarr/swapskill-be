<?php

namespace App\Providers;

use App\Domain\Posts\Contracts\PostRepository;
use App\Domain\Skills\Contracts\SkillRepository;
use App\Infrastructure\Persistence\EloquentPostRepository;
use App\Infrastructure\Persistence\EloquentSkillRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PostRepository::class, EloquentPostRepository::class);
        $this->app->bind(SkillRepository::class, EloquentSkillRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
