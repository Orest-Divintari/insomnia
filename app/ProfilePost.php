<?php

namespace App;

use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Traits\FormatsDate;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ProfilePost extends Model
{
    use FormatsDate, RecordsActivity, Searchable;

    /**
     * Number of visible posts per page
     *
     * @var int
     */
    const PER_PAGE = 3;

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
    protected $appends = ['date_created', 'type'];

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

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
    public function addComment($comment, $poster)
    {

        $newComment = $this->comments()->create($comment);
        event(new NewCommentWasAddedToProfilePost(
            $this,
            $newComment,
            $poster,
            $this->profileOwner,
        ));

        return $newComment;
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
     * @return Builder
     */
    public function scopeWithSearchInfo($query)
    {
        return $query->with(['poster', 'profileOwner']);
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
}