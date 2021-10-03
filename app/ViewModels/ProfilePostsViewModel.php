<?php

namespace App\ViewModels;

use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\User;

class ProfilePostsViewModel
{
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
     * Filters to be applied on the profile posts
     *
     * @var FilterManager
     */
    protected $profilePostFilters;

    /**
     * Create a new instance of ProfileViewModel
     *
     * @param string $username
     * @param User $authUser
     * @param ExcludeIgnoredFilter $excludeIgnoredFilter
     */
    public function __construct($authUser, $excludeIgnoredFilter, $profilePostFilters)
    {
        $this->authUser = $authUser;
        $this->excludeIgnoredFilter = $excludeIgnoredFilter;
        $this->profilePostFilters = $profilePostFilters;
    }

    /**
     * Fetch the profile posts for the given user
     *
     * @param User $user
     * @return Builder
     */
    public function profilePosts()
    {
        $profilePosts = ProfilePost::query()
            ->excludeIgnored($this->authUser, $this->excludeIgnoredFilter)
            ->filter($this->profilePostFilters)
            ->with('profileOwner')
            ->withLikes()
            ->paginate(ProfilePost::PER_PAGE);

        foreach ($profilePosts->items() as $profilePost) {

            $comments = Reply::query()
                ->where('repliable_id', $profilePost->id)
                ->where('repliable_type', get_class($profilePost))
                ->excludeIgnored($this->authUser, $this->excludeIgnoredFilter)
                ->withLikes()
                ->latest('created_at')
                ->paginate(ProfilePost::REPLIES_PER_PAGE)
                ->withPath(route('ajax.comments.index', $profilePost));

            foreach ($comments->items() as $comment) {
                $comment->append('permissions');

            }

            $profilePost->unignoredComments = $comments;

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