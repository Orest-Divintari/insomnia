<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class ThreadSubscription extends Model
{
    const WITH_EMAILS = true;
    const WITHOUT_EMAILS = false;

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'prefers_email' => 'boolean',
    ];

    /**
     * Get the user associated with the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the thread associated with the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the subscription for the given user and thread
     *
     * @param int $threadId
     * @param int $userId
     * @return Model
     */
    public static function Of($threadId, $userId = null)
    {
        return ThreadSubscription::where([
            'thread_id' => $threadId,
            'user_id' => $userId ?? auth()->id(),
        ])->firstOrFail();
    }

    /**
     * Disable email notifications for a specific subscription
     *
     * @return void
     */
    public function enableEmailNotifications()
    {
        $this->update([
            'prefers_email' => true,
        ]);
    }

    /**
     * Disable email notifications for a specific subscription
     *
     * @return void
     */
    public function disableEmailNotifications()
    {
        $this->update([
            'prefers_email' => false,
        ]);
    }

    /**
     * Determine whether the user prefers to receive email notifications
     *
     * @return bool
     */
    public function prefersEmails()
    {
        return $this->prefers_email;
    }

}