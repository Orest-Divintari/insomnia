<?php

namespace App\Filters;

use App\Conversation;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class ExcludeIgnoredFilter
{
    /**
     * Supported models
     *
     * @var array
     */
    protected $models = [
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
     * The authenticated user
     *
     * @var User
     */
    protected $authUser;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  User|null  $authUser
     * @return Builder
     */
    public function apply($builder, $authUser)
    {
        if (!auth()->check()) {
            return $builder;
        }

        $this->model = $builder->getModel();
        $this->authUser = $authUser;

        $forModel = $this->models[get_class($this->model)];

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
        return $this->exceptFromIgnoredUsers($builder)
            ->whereRaw(
                'threads.id NOT IN
                (
                    SELECT ignorable_id
                    FROM   ignorations
                    WHERE  ignorable_type=?
                    AND    user_id=?
                )',
                [Thread::class, $this->authUser->id]
            );
    }

    /**
     * Exclude replies that are created by ignored users
     *
     * @param $builder
     * @return Builder
     */
    public function replies($builder)
    {
        return $this->exceptFromIgnoredUsers($builder);
    }

    /**
     * Exlude conversations that are created by ignored users
     *
     * @param $builder
     * @return Builder
     */
    public function conversations($builder)
    {
        return $this->exceptFromIgnoredUsers($builder);
    }

    /**
     * Exclude profile posts that are created by ignored users
     *
     * @param $builder
     * @return Builder
     */
    public function profilePosts($builder)
    {
        return $this->exceptFromIgnoredUsers($builder);
    }

    /**
     * Exclude the items that are created by ignored users
     *
     * @param Builder $builder
     * @return Builder
     */
    protected function exceptFromIgnoredUsers($builder)
    {
        $table = $this->model->getTable();

        return $builder->whereRaw(
            $table . '.user_id NOT IN
            (
                SELECT ignorable_id
                FROM ignorations
                WHERE ignorable_type=?
                AND user_id=?
            )',
            [User::class, $this->authUser->id]
        );
    }
}