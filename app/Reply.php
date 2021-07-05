<?php

namespace App;

use App\Helpers\Facades\ResourcePath;
use App\Queries\CreatorIgnoredByVisitorColumn;
use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\Likeable;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{
    use Filterable,
    Likeable,
    FormatsDate,
    RecordsActivity,
        Searchable;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_updated',
        'date_created',
        'type',
    ];

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relationships to always eager-load
     *
     * @var array
     */
    protected $with = ['poster'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['repliable'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'position' => 'int',
        'is_liked' => 'boolean',
        'creator_ignored_by_visitor' => 'boolean',
    ];

    /**
     * A reply belongs to a repliable model
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function repliable()
    {
        return $this->morphTo();
    }

    /**
     * A reply belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');

    }

    /**
     * Sanitize the body from malicious context
     *
     * @param string $body
     * @return string
     */
    public function getBodyAttribute($body)
    {
        return Purify::clean($body);
    }

    /**
     * Get the url the reply can be found
     *
     * @return string
     */
    public function getPathAttribute()
    {
        return ResourcePath::generate($this);
    }

    /**
     * Get the activities of the reply
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Get the indexable data array for the model
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->withoutRelations()->toArray();
        if ($this->isComment()) {
            $array['profile_owner_id'] = $this->repliable->profile_owner_id;
        }
        return $array;
    }

    /**
     * Get the type of the model
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        if ($this->repliable_type == 'App\Thread') {
            return 'thread-reply';
        } elseif ($this->repliable_type == 'App\ProfilePost') {
            return 'profile-post-comment';
        } elseif ($this->repliable_type == 'App\Conversation') {
            return 'conversation-message';
        }
    }

    /**
     * Determine if it is a thread reply
     *
     * @return boolean
     */
    public function isThreadReply()
    {
        return $this->repliable_type === Thread::class;
    }

    /**
     * Determine whether the reply consists the body of the thread
     *
     * @return boolean
     */
    public function isThreadBody()
    {
        return $this->position === 1;
    }

    /**
     * Determine if the reply is a profile post comment
     *
     * @return boolean
     */
    public function isComment()
    {
        return $this->repliable_type === ProfilePost::class;
    }

    /**
     * Determine if the reply is a conversation message
     *
     * @return boolean
     */
    public function isMessage()
    {
        return $this->repliable_type === Conversation::class;
    }

    /**
     * Determine what should be serchable for algolia
     *
     * @return boolean
     */
    public function shouldBeSearchable()
    {
        // The first thread-reply consists the body of the thread
        // therefore it should not be searchable
        if ($this->isThreadReply()) {
            return !$this->isThreadBody();
        }

        if ($this->isMessage()) {
            return false;
        }

        return true;
    }

    /**
     * Get the informatiomn that is required to display a thread reply or a comment
     * as a search result with algolia
     *
     * @param Builer $query
     * @param User|null $authUser
     * @return Builer
     */
    public function scopeWithSearchInfo($query, $authUser = null)
    {
        return $query
            ->withCreatorIgnoredByVisitor($authUser)
            ->with(['repliable' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Thread::class => ['poster', 'category'],
                    ProfilePost::class => ['profileOwner'],
                ]);
            }]);
    }

    /**
     * Determine if the model should be recordable
     *
     * @return boolean
     */
    public function shouldBeRecordable()
    {
        if ($this->isMessage() || $this->isThreadBody()) {
            return false;
        }
        return true;
    }

    /**
     * Associate a user to the reply
     *
     * @param User $user
     * @return Reply
     */
    public function setPoster($user)
    {
        return $this->poster()->associate($user);
    }

    /**
     * Append the permissions of the authenticated user
     *
     * @return array
     */
    public function getPermissionsAttribute()
    {
        if (!auth()->check()) {
            return;
        }
        return [
            'update' => auth()->user()->can('update', $this),
            'delete' => auth()->user()->can('delete', $this),
        ];
    }

    /**
     * Add column that determines whether it is ignored by the authenticated user
     *
     * @param Builder $query
     * @param Bool $authUser
     * @return Builder
     */
    public function scopeWithCreatorIgnoredByVisitor($query, $authUser)
    {
        $column = app(CreatorIgnoredByVisitorColumn::class);

        return $column->addSelect($query, $authUser);
    }

    /**
     * Filter out the replies that are created by ignored users
     *
     * @param Builder $builder
     * @param User $authUser
     * @param ExcludeIngoredFilter $excludeIgnoredFilter
     * @return Builder
     */
    public function scopeExcludeIgnored($builder, $authUser, $excludeIgnoredFilter)
    {
        return $excludeIgnoredFilter->apply($builder, $authUser);
    }
}