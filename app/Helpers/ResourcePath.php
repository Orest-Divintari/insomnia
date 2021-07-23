<?php

namespace App\Helpers;

use App\Models\ProfilePost;
use App\Models\Reply;

class ResourcePath
{
    protected $paths = [
        Reply::class => 'reply',
        ProfilePost::class => 'profilePost',
    ];

    protected $pageNumber = [
        'profile-post' => 'profilePostPageNumber',
        'comment' => 'commentPageNumber',
        'reply' => 'threadReplyPageNumber',
        'message' => 'messagePageNumber',
    ];

    /**
     * Generate the path for the given resource
     *
     * @param mixed $resource
     * @return string
     */
    public function generate($resource)
    {
        $path = $this->paths[get_class($resource)];

        return $this->$path($resource);
    }

    /**
     * Get the page number the model belongs to, according to pagination
     *
     * @param mixed $model
     * @return int
     */
    public function pageNumber($model)
    {
        $pageNumberFor = $this->pageNumber[ModelType::get($model)];

        return $this->$pageNumberFor($model);
    }

    /**
     * Get the path of the reply according to the type of the reply
     *
     * @param Reply $reply
     * @return string
     */
    private function reply($reply)
    {
        if ($reply->isComment()) {
            return $this->comment($reply);
        } elseif ($reply->isThreadReply()) {
            return $this->threadReply($reply);
        } elseif ($reply->isMessage()) {
            return $this->message($reply);
        }
    }

    /**
     * Get the path of the given thread reply
     *
     * @param Reply $reply
     * @return string
     */
    private function threadReply($reply)
    {
        $path = route('threads.show', $reply->repliable);

        $pageNumber = $this->threadReplyPageNumber($reply);

        if ($pageNumber > 1) {
            $path = $path . '?page=' . $pageNumber;
        }

        return $path . '#post-' . $reply->id;
    }

    /**
     * Get the path of the given message
     *
     * @param Reply $message
     * @return string
     */
    private function message($message)
    {
        return route('conversations.show', $message->repliable) .
        "?page=" . $this->messagePageNumber($message) .
        '#convMessage-' . $message->id;
    }

    /**
     * Get the path of the given profile post comment
     *
     * @param Reply $comment
     * @return string
     */
    private function comment($comment)
    {
        $profilePost = $comment->repliable;

        $profilePostPageNumber = $this->profilePostPageNumber($profilePost);

        $commentUrl = route('profiles.show', $profilePost->profileOwner);

        if ($profilePostPageNumber > 1) {
            $commentUrl = $commentUrl . '?page=' . $profilePostPageNumber;
        }

        return $commentUrl . '#profile-post-' . $profilePost->id;
    }

    /**
     * Get the path of the given profile post
     *
     * @param ProfilePost $profilePost
     * @return string
     */
    private function profilePost($profilePost)
    {
        $url = route('profiles.show', $profilePost->profileOwner);

        $pageNumber = $this->profilePostPageNumber($profilePost);

        if ($pageNumber > 1) {
            $url = $url . '?page=' . $pageNumber;
        }

        return $url . '#profile-post-' . $profilePost->id;
    }

    /**
     * Get the page number the message is positioned to
     *
     * @param Reply $message
     * @return int
     */
    private function messagePageNumber($message)
    {
        $numberOfRepliesBefore = Reply::where(
            'repliable_type', get_class($message->repliable)
        )->where('repliable_id', $message->repliable->id)
            ->where('id', '<', $message->id)
            ->count();

        return (int) ceil($numberOfRepliesBefore / $message->repliable::REPLIES_PER_PAGE);
    }

    /**
     * Get the page number the comment is positioned to
     *
     * @param Reply $comment
     * @return int
     */
    private function commentPageNumber($comment)
    {
        $numberOfRepliesBefore = Reply::where(
            'repliable_type', get_class($comment->repliable)
        )->where('repliable_id', $comment->repliable->id)
            ->where('id', '<', $comment->id)
            ->count();

        return (int) ceil($numberOfRepliesBefore / $comment->repliable::REPLIES_PER_PAGE);
    }

    /**
     * Get the page number the thread reply is positioned to
     *
     * @param Reply $reply
     * @return int
     */
    private function threadReplyPageNumber($reply)
    {
        $numberOfReplies = Reply::where(
            'repliable_type', get_class($reply->repliable)
        )->where('repliable_id', $reply->repliable->id)
            ->where('id', '<=', $reply->id)
            ->count();

        return (int) ceil($numberOfReplies / $reply->repliable::REPLIES_PER_PAGE);
    }

    /**
     * Get the page number the profile post is positioned to
     *
     * @param ProfilePost $profilePost
     * @return int
     */
    private function profilePostPageNumber($profilePost)
    {
        $numberOfPreviousPosts = ProfilePost::where('id', '<', $profilePost->id)->count();

        return (int) ceil($numberOfPreviousPosts / ProfilePost::PER_PAGE);
    }

}
