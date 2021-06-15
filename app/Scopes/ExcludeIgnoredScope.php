<?php

namespace App\Scopes;

use App\Conversation;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExcludeIgnoredScope implements Scope
{

    /**
     * Apply the scope
     */
    static $models = [
        Reply::class => 'replies',
        Thread::class => 'threads',
        ProfilePost::class => 'profilePosts',
        Conversation::class => 'conversations',
    ];

    /**
     * The model of the resource that will be excluded
     *
     * @var mixed
     */
    protected $model;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return Builder
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!auth()->check()) {
            return $builder;
        }

        $this->model = $model;

        $forModel = static::$models[get_class($model)];

        return $this->$forModel($builder);
    }

    /**
     * Exclude threads that are either directly ignored
     * or are created by ignored users
     *
     * @param $builder
     * @return Builder
     */
    public function threads($builder)
    {
        $ignorings = auth()->user()->ignorings;
        $ignoredUserIds = $ignorings
            ->where('ignorable_type', User::class)
            ->pluck('ignorable_id');
        $ignoredThreadIds = $ignorings
            ->where('ignorable_type', Thread::class)
            ->pluck('ignorable_id');

        return $builder
            ->whereNotIn("{$this->model->getTable()}.id", $ignoredThreadIds)
            ->whereNotIn("{$this->model->getTable()}.user_id", $ignoredUserIds);
    }

    /**
     * Exclude profile posts that are created by ignored users
     *
     * @param $builder
     * @return Builder
     */
    public function profilePosts($builder)
    {
        return $builder->whereNotIn(
            "{$this->model->getTable()}.user_id"
            , auth()->user()->ignoredUserIds()
        );
    }

    /**
     * Exlude conversations that are created by ignored users
     *
     * @param $builder
     * @return Builder
     */
    public function conversations($builder)
    {
        return $builder->whereNotIn(
            "{$this->model->getTable()}.user_id"
            , auth()->user()->ignoredUserIds()
        );
    }

    /**
     * Exclude thread replies that are created by ignored users
     *
     * @param $builder
     * @return Builder
     */
    public function replies($builder)
    {
        return $builder->whereNotIn(
            "{$this->model->getTable()}.user_id"
            , auth()->user()->ignoredUserIds()
        );
    }
}