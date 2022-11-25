<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Publisher' => 'App\Policies\PublisherPolicy',
        'App\Models\Book' => 'App\Policies\BookPolicy',
        'App\Models\Author' => 'App\Policies\AuthorPolicy',
    ];


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            // url front end should be changed to spaUrl
            $url = str_replace(url('/api'), env('SPA_URL'), $url);

            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            $spaUrl = env('SPA_URL') ? env('SPA_URL') : config('app.url');
            $spaUrl .= '/reset-password?token=' . $token;

            return $spaUrl;
        });
    }
}
