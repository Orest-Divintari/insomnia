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
        Thread::class => 'getThreadType',
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

        if (method_exists(static::class, $type)) {
            return static::$type($model);
        }

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
     * Adds "created-" suffix  to the given model type
     *
     * @param mixed $model
     * @return string
     */
    public static function prefixCreated($model)
    {
        return "created-" . static::get($model);
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
        return static::classType($model);
    }

    /**
     * Get the type of a thread
     *
     * @param Thread $model
     * @return string
     */
    protected static function getThreadType($model)
    {
        return static::classType($model);
    }

    /**
     * Get the type based on the class name
     *
     * @param mixed $model
     * @return string
     */
    protected static function classType($model)
    {
        $class = class_basename($model);

        $class = ltrim(implode(' ', preg_split('/(?=[A-Z])/', $class)));

        return strtolower(implode("-", explode(" ", $class)));
    }

}