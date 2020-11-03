<?php

namespace App;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use Sluggable;
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * A conversation may have many participants
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(
            User::class,
            'conversation_participants',
            'conversation_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Add participants to converastion
     *
     * @param string[] $usernames
     * @return void
     */
    public function addParticipants($usernames)
    {
        $participantIds = $this
            ->getParticipantIds($usernames);

        $this->participants()
            ->attach($participantIds);
    }

    /**
     * Get the ids for the given username or usernames
     *
     * @param string[] $usernames
     * @return int[]
     */
    public function getParticipantIds($usernames)
    {
        return User::whereIn('name', $usernames)
            ->pluck('id');
    }

    /**
     * A conversation may have many messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function messages()
    {
        return $this->morphMany(Reply::class, 'repliable');
    }

    /**
     * Add a new message to conversation
     *
     * @param string $message
     * @param User|null $user
     * @return Message
     */
    public function addMessage($message, $user = null)
    {
        return $this->messages()
            ->create([
                'body' => $message,
                'user_id' => $user ? $user->id : auth()->id(),
            ]);
    }
}