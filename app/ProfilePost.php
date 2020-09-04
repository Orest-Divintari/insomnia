<?php

namespace App;

use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Traits\FormatsDate;
use Illuminate\Database\Eloquent\Model;

class ProfilePost extends Model
{
    use FormatsDate;

    /**
     * Number of visible posts per page
     *
     * @var int
     */
    const PER_PAGE = 3;

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
    protected $appends = ['date_created'];

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
        return $this->belongsTo(User::class);
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

}