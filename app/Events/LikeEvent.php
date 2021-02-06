<?php

namespace App\Events;

use App\Conversation;
use App\Events\Conversation\MessageWasLiked;
use App\Events\Profile\CommentWasLiked;
use App\Events\Subscription\ReplyWasLiked;
use App\ProfilePost;
use App\Thread;

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
     * The reply that was liked
     *
     * @var Reply
     */
    protected $reply;

    /**
     * Create a new LikeEvent instance
     *
     * @param User $liker
     * @param Reply $reply
     * @param Like $like
     */
    public function __construct($liker, $reply, $like)
    {
        $this->liker = $liker;
        $this->reply = $reply;
        $this->like = $like;
    }

    /**
     * Create an event
     *
     * @return Event
     */
    public function create()
    {
        if ($this->isComment()) {
            return $this->commentWasLiked();
        } elseif ($this->isThreadReply()) {
            return $this->threadReplyWasLiked();
        } elseif ($this->isMessage()) {
            return $this->messageWasLiked();
        }
    }

    /**
     * Determine if it is a thread reply
     *
     * @return boolean
     */
    public function isThreadReply()
    {
        return $this->reply->repliable_type === Thread::class;
    }

    /**
     * Determine if the reply is a profile post comment
     *
     * @return boolean
     */
    public function isComment()
    {
        return $this->reply->repliable_type === ProfilePost::class;
    }

    /**
     * Determine if the reply is a conversation message
     *
     * @return boolean
     */
    public function isMessage()
    {
        return $this->reply->repliable_type === Conversation::class;
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
            $this->reply->repliable,
            $this->reply
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
            $this->reply,
            $this->reply->poster,
            $this->reply->repliable,
            $this->reply->repliable->profileOwner
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
            $this->reply->repliable,
            $this->reply,
            $this->reply->poster
        );
    }
}