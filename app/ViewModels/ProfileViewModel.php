<?php

namespace App\ViewModels;

use App\ProfilePost;
use App\Reply;
use App\User;

class ProfileViewModel
{

    /**
     * The profile owner
     *
     * @var User
     */
    protected $user;

    /**
     * The authenticated user
     *
     * @var User
     */
    protected $authUser;

    /**
     * The filter that excludes items created by ignored users
     *
     * @var ExcludeIgnoredFilter
     */
    protected $excludeIgnoredFilter;

    /**
     * The username of the profile owner
     *
     * @var string
     */
    protected $username;

    /**
     * Create a new instance of ProfileViewModel
     *
     * @param string $username
     * @param User $authUser
     * @param ExcludeIgnoredFilter $excludeIgnoredFilter
     */
    public function __construct($username, $authUser, $excludeIgnoredFilter)
    {
        $this->username = $username;
        $this->authUser = $authUser;
        $this->excludeIgnoredFilter = $excludeIgnoredFilter;
    }

    /**
     * Fetch the profile posts for the given user
     *
     * @param User $user
     * @return Builder
     */
    public function profilePosts($user)
    {
        $profilePosts = $user->profilePosts()
            ->excludeIgnored($this->authUser, $this->excludeIgnoredFilter)
            ->withLikes()
            ->latest()
            ->paginate(ProfilePost::PER_PAGE);

        foreach ($profilePosts->items() as $profilePost) {

            $comments = Reply::query()
                ->where('repliable_id', $profilePost->id)
                ->where('repliable_type', get_class($profilePost))
                ->excludeIgnored($this->authUser, $this->excludeIgnoredFilter)
                ->withLikes()
                ->latest('id')
                ->paginate(ProfilePost::REPLIES_PER_PAGE)
                ->withPath(route('ajax.comments.index', $profilePost));

            foreach ($comments->items() as $comment) {
                $comment->append('permissions');
            }

            $profilePost->unignoredCOmments = $comments;

            $profilePost->append('paginatedComments');
        }

        return $profilePosts;
    }

    /**
     * Fetch the profile owner
     *
     * @return User
     */
    public function user()
    {
        return User::query()
            ->findByName($this->username)
            ->withProfileInfo($this->authUser)
            ->firstOrFail()
            ->append('join_date');
    }
}