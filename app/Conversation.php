<?php

namespace App;

use App\Events\Conversation\NewMessageWasAddedToConversation;
use App\Events\Conversation\NewParticipantsWereAdded;
use App\Events\Conversation\ParticipantWasRemoved;
use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\Lockable;
use App\Traits\Readable;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use Sluggable,
    FormatsDate,
    Filterable,
    Lockable,
        Readable;

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
        'date_updated',
        'type',
    ];

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'locked' => 'boolean',
        'has_been_updated' => 'boolean',
        'starred' => 'boolean',
    ];

    /**
     * Get the route key name
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

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
        )->isConversationAdmin($this)
            ->withTimestamps();
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
     * Remove participant from conversation
     *
     * @return void
     */
    public function removeParticipant($participantId)
    {
        $this->participants()->detach($participantId);

        event(new ParticipantWasRemoved($this, $participantId));
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

    /**
     * Add a column with the date the conversation was read
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithRead($query)
    {
        return $query->addSelect(['read_at' => Read::select('reads.read_at')
                ->whereColumn('reads.readable_id', 'conversations.id')
                ->where('reads.readable_type', '=', 'App\Conversation')
                ->where('reads.user_id', '=', auth()->id()),
        ]);
    }

    /**
     * Sort converations by unread
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrderByUnread($query)
    {
        return $query
            ->withRead()
            ->orderByRaw('
                CASE
                    WHEN read_at is NULL THEN 1
                    WHEN conversations.updated_at > read_at THEN 1
                    ELSE 0
                END DESC'
            );
    }

    /**
     * Sort conversations by the date that it was updated
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrderByUpdated($query)
    {
        return $query->orderBy('conversations.updated_at', 'DESC');
    }

    /**
     * Set participant as admin
     *
     * @param int $participantId
     * @param boolean $admin
     * @return void
     */
    public function setAdmin($participantId, $admin = true)
    {
        ConversationParticipant::where('conversation_id', $this->id)
            ->where('user_id', $participantId)
            ->update(['admin' => $admin]);
    }

    /**
     * Determine whether the given user is a admin of the conversation
     *
     * @param User $participant
     * @return boolean
     */
    public function isAdmin($participant)
    {
        return ConversationParticipant::where('conversation_id', $this->id)
            ->where('user_id', $participant->id)
            ->first()
            ->admin;
    }

    /**
     * Remove participant as admin
     *
     * @param int $participantId
     * @return void
     */
    public function removeAdmin($participantId)
    {
        $this->setAdmin($participantId, $admin = false);
    }

    /**
     * Star the conversation
     *
     * @param boolean $starred
     * @return void
     */
    /** @test */
    public function star($starred = true)
    {
        ConversationParticipant::where('conversation_id', $this->id)
            ->where('user_id', auth()->id())
            ->update(['starred' => $starred]);
    }

    /**
     * Unstar the conversation
     *
     * @return void
     */
    public function unstar()
    {
        $this->star($starred = false);
    }

    /**
     * Add a column which determines whether the conversation is starred
     * by the current user
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsStarred($query)
    {
        return $query->addSelect([
            'starred' => ConversationParticipant::select('starred')
                ->whereColumn('conversation_id', 'conversations.id')
                ->where('user_id', auth()->id()),
        ]);
    }

    /**
     * Determine whether the authenticated has starred the conversation
     *
     * @return boolean
     */
    public function starred()
    {
        return ConversationParticipant::where('conversation_id', $this->id)
            ->where('user_id', auth()->id())
            ->firstOrFail()
            ->starred;
    }

}