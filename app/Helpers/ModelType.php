<?php

namespace App\Helpers;

use App\Conversation;
use App\Like;
use App\ProfilePost;
use App\Reply;
use App\Thread;

class ModelType
{

    public static $types = [
        Like::class => 'getLikeableType',
        Reply::class => 'getReplyType',
        ProfilePost::class => 'getProfilePostType',
    ];

    /**
     * Get the type of the given model
     *
     * @param mixed $model
     * @return string
     */
    public static function get($model)
    {
        $type = static::$types[get_class($model)];

        return static::$type($model);
    }

    /**
     * Append the "-like" suffix to the given model type
     *
     * @param mixed $model
     * @return string
     */
    public static function like($model)
    {
        return static::get($model) . '-like';
    }

    /**
     * Get the type of the model that was liked
     *
     * @param mixed $model
     * @return string
     */
    protected static function getLikeableType($model)
    {
        return static::like($model->likeable);
    }

    /**
     * Get the type of the reply
     *
     * @param Reply $model
     * @return string
     */
    protected static function getReplyType($model)
    {
        if ($model->repliable_type == ProfilePost::class) {
            $type = 'comment';
        } elseif ($model->repliable_type == Thread::class) {
            $type = 'reply';
        } elseif ($model->repliable_type == Conversation::class) {
            $type = 'message';
        }

        return $type;
    }

    /**
     * Get the type of a profile post
     *
     * @param ProfilePost $model
     * @return string
     */
    protected static function getProfilePostType($model)
    {
        return 'profile-post';
    }

}