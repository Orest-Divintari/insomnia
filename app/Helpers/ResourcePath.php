<?php

namespace App\Helpers;

use App\ProfilePost;
use App\Reply;

class ResourcePath
{
    protected $paths = [
        Reply::class => 'reply',
        ProfilePost::class => 'profilePost',
    ];

    protected $pageNumbers = [
        Reply::class => 'replyPageNumber',
        ProfilePost::class => 'profilePostPageNumber',
    ];

    public function generate($resource)
    {
        $path = $this->paths[get_class($resource)];

        return $this->$path($resource);
    }

    protected function reply($reply)
    {
        if ($reply->isComment()) {
            return $this->comment($reply);
        } elseif ($reply->isThreadReply()) {
            return $this->threadReply($reply);
        } elseif ($reply->isMessage()) {
            return $this->message($reply);
        }
    }

    protected function threadReply($reply)
    {
        $path = route('threads.show', $reply->repliable);

        $pageNumber = $this->pageNumber($reply);

        if ($pageNumber > 1) {
            $path = $path . '?page=' . $pageNumber;
        }

        return $path . '#post-' . $reply->id;
    }

    public function message($message)
    {
        return route('conversations.show', $message->repliable) .
        "?page=" . $this->pageNumber($message) .
        '#convMessage-' . $message->id;
    }

    protected function comment($comment)
    {
        $post = $comment->repliable;
        $pageNumber = $this->pageNumber($post);
        $commentUrl = route('profiles.show', $post->profileOwner);

        if ($pageNumber > 1) {
            $commentUrl = $commentUrl . '?page=' . $pageNumber;
        }

        return $commentUrl . '#profile-post-comment-' . $comment->id;
    }

    protected function profilePost($profilePost)
    {
        $url = route('profiles.show', $profilePost->profileOwner);

        $pageNumber = $this->pageNumber($profilePost);

        if ($pageNumber > 1) {
            $url = $url . '?page=' . $pageNumber;
        }

        return $url . '#profile-post-' . $profilePost->id;
    }

    protected function replyPageNumber($reply)
    {
        $numberOfRepliesBefore = Reply::where(
            'repliable_type', get_class($reply->repliable)
        )->where('repliable_id', $reply->repliable->id)
            ->where('id', '<', $reply->id)
            ->count();

        return (int) ceil($numberOfRepliesBefore / $reply->repliable::REPLIES_PER_PAGE);
    }

    protected function profilePostPageNumber($profilePost)
    {
        $numberOfPreviousPosts = ProfilePost::where('id', '<', $profilePost->id)->count();

        return (int) ceil($numberOfPreviousPosts / ProfilePost::PER_PAGE);
    }

    public function pageNumber($resource)
    {
        $model = get_class($resource);

        $pageNumber = $this->pageNumbers[$model];

        return $this->$pageNumber($resource);
    }
}