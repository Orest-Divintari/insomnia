<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Thread' => 'App\Policies\ThreadPolicy',
        'App\Models\Reply' => 'App\Policies\ReplyPolicy',
        'App\Models\Comment' => 'App\Policies\CommentPolicy',
        'App\Models\Conversation' => 'App\Policies\ConversationPolicy',
        User::class => 'App\Policies\UserPolicy',
        'App\Models\Activity' => 'App\Policies\ActivityPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

}