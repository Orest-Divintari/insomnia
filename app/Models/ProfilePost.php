<?php

namespace App\Models;

use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Helpers\Facades\ResourcePath;
use App\Queries\CreatorIgnoredByVisitorColumn;
use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\Likeable;
use App\Traits\RecordsActivity;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ProfilePost extends Model
{
    use FormatsDate,
    RecordsActivity,
    Searchable,
    QueryDsl,
    Likeable,
    Filterable,
        HasFactory;

    /**
     * Number of visible posts per page
     *
     * @var int
     */
    const PER_PAGE = 5;

    /**
     * Number of profile post comments per page
     *
     * @var int
     */
    const REPLIES_PER_PAGE = 3;

    /**
     * Relationships to always eager-laod
     *
     * @var array
     */
    protected $with = ['poster'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['date_created', 'type', 'permissions'];

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
        'creator_ignored_by_visitor' => 'boolean',
    ];

    /**
     * Comments of the profile post that are not created by ignored users
     *
     * @var Illuminate\Pagination\LengthAwarePaginator;
     */
    public $unignoredComments;

    /**
     * A profile post has an owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Add a new comment to current profile post
     *
     * @param array $comment
     * @return Comment
     */
    public function addComment($body, $poster = null)
    {
        $poster = $poster ?: auth()->user();

        $comment = $this->comments()->save(
            (new Reply($body))->setPoster($poster)
        );

        event(new NewCommentWasAddedToProfilePost(
            $this,
            $comment,
            $poster,
            $this->profileOwner,
        ));

        return $comment;
    }

    /**
     * Get the comments associated with the post
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Reply::class, 'repliable');
    }

    // /**
    //  * Get the comments that are created by users that are not ignored
    //  * by the authenticated user
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\MorphMany
    //  */
    // public function unignoredComments()
    // {

    //     return $this->morphMany(Reply::class, 'repliable')
    //         ->whereNotIn('user_id', auth()->user()->ignoredUserIds());
    // }

    /**
     * Get the owner of the profile in which the post was posted
     *
     * @return void
     */
    public function profileOwner()
    {
        return $this->belongsTo(User::class, 'profile_owner_id');
    }

    /**
     * Get the activities of the profile post
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getTypeAttribute()
    {
        return 'profile-post';
    }

    /**
     * Get the information that is required to display a profile post
     * as as search result with algolia
     *
     * @param Builder $query
     * @param User|null $authUser
     * @return Builder
     */
    public function scopeWithSearchInfo($query, $authUser = null)
    {
        return $query
            ->withCreatorIgnoredByVisitor($authUser)
            ->with(['poster', 'profileOwner']);
    }

    /**
     * Determine if the activity for this model should be recorded
     *
     * @return boolean
     */
    public function shouldBeRecordable()
    {
        return true;
    }

    /**
     * Append unignored comments pagination for the current profile post
     *
     * @return
     */
    public function getPaginatedCommentsAttribute()
    {
        return $this->unignoredComments;
    }

    public function getPathAttribute()
    {
        return ResourcePath::generate($this);
    }

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
     * Filter out the profile posts that are created by users that are ignored
     * by the authenticated user
     *
     * @param Builder $query
     * @param User $authUser
     * @param ExcludeIgnoredFilter $excludeIgnoredFilter
     * @return Builder
     */
    public function scopeExcludeIgnored($query, $authUser, $excludeIgnoredFilter)
    {
        return $excludeIgnoredFilter->apply($query, $authUser);
    }

    /**
     * Add column which determines whether the creator of the profile post
     * is ignored by the authenticated user
     *
     * @param Builder $query
     * @param User $authUser
     * @return Builder
     */
    public function scopeWithCreatorIgnoredByVisitor($query, $authUser)
    {
        $column = app(CreatorIgnoredByVisitorColumn::class);

        return $column->addSelect($query, $authUser);
    }
}