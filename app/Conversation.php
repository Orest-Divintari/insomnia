<?php

namespace App;

use App\Events\Conversation\NewMessageWasAddedToConversation;
use App\Events\Conversation\NewParticipantsWereAdded;
use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use Sluggable, FormatsDate, Filterable;

    /**
     * Number of conversations per page
     */
    const PER_PAGE = 10;

    /**
     * Number of conversation messages per page
     *
     * @var int
     */
    const REPLIES_PER_PAGE = 15;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_created',
        'type',
        'has_been_updated',
    ];
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
     * Get the participants that have not left the conversation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activeParticipants()
    {
        return $this->belongsToMany(
            User::class,
            'conversation_participants',
            'conversation_id',
            'user_id'
        )->wherePivot('left', false)
            ->withTimestamps();
    }

    /**
     * Add participants to converastion
     *
     * @param string[] $usernames
     * @param bool $admin
     * @return void
     */
    public function addParticipants(array $usernames, $admin = false)
    {
        $participantIds = $this->getParticipantIds($usernames);

        foreach ($participantIds->toArray() as $participantId) {
            $participants[$participantId] = ['admin' => $admin];
        }

        $this->participants()->syncWithoutDetaching($participants);

        event(new NewParticipantsWereAdded($this, $participantIds));
    }

    /**
     * Get the ids for the given username or usernames
     *
     * @param string[] $usernames
     * @return Collection
     */
    public function getParticipantIds(array $usernames)
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
        $message = $this->messages()
            ->create([
                'body' => $message,
                'user_id' => $user ? $user->id : auth()->id(),
            ]);

        event(new NewMessageWasAddedToConversation($this, $message));

        return $message;
    }

    /**
     * Get the user who started the conversation
     *
     * @return void
     */
    public function starter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the most recent message of the conversation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recentMessage()
    {
        return $this->belongsTo(Reply::class);
    }

    /**
     * Eager load the most recent message of the conversation
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeWithRecentMessage(Builder $query)
    {
        return $query->addSelect([
            'recent_message_id' => Reply::select('id')
                ->whereColumn('repliable_id', 'conversations.id')
                ->where('repliable_type', 'App\Conversation')
                ->latest('created_at')
                ->take(1),
        ])->with('recentMessage.poster');
    }

    /**
     * Get the type of the model
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return 'conversation';
    }

    /**
     * Determine whether the conversation has been updated
     * since the last time that was read by the authenticated user
     *
     * @return boolean
     */
    public function getHasBeenUpdatedAttribute()
    {
        $conversationRead = $this->reads()
            ->where('user_id', auth()->id())
            ->first();

        if (is_null($conversationRead)) {
            return true;
        }
        return $this->updated_at > $conversationRead->read_at;
    }

    /**
     * A conversation can be read by many users
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function reads()
    {
        return $this->morphMany(Read::class, 'readable');
    }

    /**
     * Hide a conversation from the given user
     *
     * @param User $user
     * @return void
     */
    public function hideFrom(User $user)
    {
        ConversationParticipant::where('conversation_id', $this->id)
            ->where('user_id', $user->id)
            ->update(['hid' => true]);
    }

    /**
     * User leaves conversation
     *
     * @param User $user
     * @return void
     */
    public function leftBy(User $user)
    {
        ConversationParticipant::where('conversation_id', $this->id)
            ->where('user_id', $user->id)
            ->update(['left' => true]);
    }

    /**
     * Make a hidden conversation visible again for the users who hid it
     *
     * @return void
     */
    public function unhide()
    {
        $userIds = ConversationParticipant::where('conversation_id', $this->id)
            ->where(['hid' => true])
            ->pluck('user_id');

        ConversationParticipant::where('conversation_id', $this->id)
            ->whereIn('user_id', $userIds)
            ->update(['hid' => false]);
    }

}