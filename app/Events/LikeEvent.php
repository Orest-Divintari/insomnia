<?php

namespace App\Events;

use App\Events\Conversation\MessageWasLiked;
use App\Events\Profile\CommentWasLiked;
use App\Events\Subscription\ProfilePostWasLiked;
use App\Events\Subscription\ReplyWasLiked;
use App\Models\Conversation;
use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Thread;

class LikeEvent
{

    /**
     * The user who liked an item
     *
     * @var User
     */
    protected $liker;

    /**
     * The like record
     *
     * @var Like
     */
    protected $like;

    /**
     * The model that was liked
     *
     * @var mixed
     */
    protected $likeable;

    /**
     * Create a new LikeEvent instance
     *
     * @param User $liker
     * @param mixed $likeable
     * @param Like $like
     */
    public function __construct($liker, $likeable, $like)
    {
        $this->liker = $liker;
        $this->likeable = $likeable;
        $this->like = $like;
    }

    /**
     * Create an event
     *
     * @return Event
     */
    public function create()
    {

        if (get_class($this->likeable) == Reply::class) {
            if ($this->isComment()) {
                return $this->commentWasLiked();
            } elseif ($this->isThreadReply()) {
                return $this->threadReplyWasLiked();
            } elseif ($this->isMessage()) {
                return $this->messageWasLiked();
            }
        } elseif (get_class($this->likeable) == ProfilePost::class) {
            return $this->profilePostWasLiked();
        }

    }

    /**
     * Determine if it is a thread reply
     *
     * @return boolean
     */
    public function isThreadReply()
    {
        return $this->likeable->repliable_type === Thread::class;
    }

    /**
     * Determine if the reply is a profile post comment
     *
     * @return boolean
     */
    public function isComment()
    {
        return $this->likeable->repliable_type === ProfilePost::class;
    }

    /**
     * Determine if the reply is a conversation message
     *
     * @return boolean
     */
    public function isMessage()
    {
        return $this->likeable->repliable_type === Conversation::class;
    }

    /**
     * Create ThreadReplyWasLiked Event
     *
     * @return ReplyWasLiked
     */
    public function threadReplyWasLiked()
    {
        return new ReplyWasLiked(
            $this->liker,
            $this->like,
            $this->likeable->repliable,
            $this->likeable
        );
    }

    /**
     * Create CommentWasLiked event
     *
     * @return CommentWasLiked
     */
    public function commentWasLiked()
    {
        return new CommentWasLiked(
            $this->liker,
            $this->like,
            $this->likeable,
            $this->likeable->poster,
            $this->likeable->repliable,
            $this->likeable->repliable->profileOwner
        );
    }

    /**
     * Create MessageWasLiked event
     *
     * @return MessageWasLiked
     */
    public function messageWasLiked()
    {
        return new MessageWasLiked(
            $this->liker,
            $this->like,
            $this->likeable->repliable,
            $this->likeable,
            $this->likeable->poster
        );
    }

    /**
     * Create ProfilePostWasLiked event
     *
     * @return ProfilePostWasLiked
     */
    protected function profilePostWasLiked()
    {
        return new ProfilePostWasLiked(
            $this->liker,
            $this->like,
            $this->likeable,
            $this->likeable->profileOwner,
            $this->likeable->poster,
        );
    }

}
