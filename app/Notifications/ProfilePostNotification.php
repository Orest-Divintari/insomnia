<?php

namespace App\Notifications;

class ProfilePostNotification
{
    /**
     * Get the channels for the given notifiable
     *
     * when a user is mentioned in a comment
     * the mention notifications have priority
     * over the other comment notifications
     *
     * @param User $notifiable
     * @param ProfilePost $post
     * @param array $channels
     * @return array
     */
    public static function channels($notifiable, $post, $channels)
    {

        if ($post->doesntHaveMentionedUser($notifiable)) {
            return $channels;
        }

        // when the given $channels = ['database']
        // and mentioned_in_profile_post = ['database]
        // it means that mention notifications are enabled
        // and since mention notifications have priority
        // an emtpy array will be returned
        // however if the given $channels = ['database']
        // and mentioned_in_profile_post = [] -> mention notifications are disabled
        // then ['database'] will be returned
        return array_diff(
            $channels,
            $notifiable->preferences()->mentioned_in_profile_post
        );
    }
}