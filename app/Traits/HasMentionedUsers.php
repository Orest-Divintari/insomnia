<?php

namespace App\Traits;

trait HasMentionedUsers
{

    /**
     * Set the body attribute
     *
     * @param string $body
     * @return void
     */
    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = $this->wrapMentionedUsersWithAnchorTags($body);
    }

    /**
     * Replace the mentioned names with links
     *
     * @param string $body
     * @return string
     */
    public function wrapMentionedUsersWithAnchorTags($body)
    {
        return preg_replace(
            '/@(\w+[\-\.\w]*\w+)/',
            '<a href="/profiles/$1">$0</a>',
            $body
        );
    }

    /**
     * Fetch all mentioned users within the body.
     *
     * @return array
     */
    public function mentionedUsers()
    {
        preg_match_all('/@(\w+[\-\.\w]+\w+)/', $this->body, $matches);

        return $matches[1];
    }

    /**
     * Determine whether the given user is mentioned
     *
     * @param User $user
     * @return boolean
     */
    public function hasMentionedUser($user)
    {
        return collect($this->mentionedUsers())->contains($user->name);
    }

    /**
     * Determine whether the given user is not mentioned
     *
     * @param User $user
     * @return boolean
     */
    public function doesntHaveMentionedUser($user)
    {
        return !$this->hasMentionedUser($user);
    }
}