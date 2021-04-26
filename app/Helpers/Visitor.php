<?php

namespace App\Helpers;

class Visitor
{

    public static function get()
    {
        if (!$user = auth()->user()) {
            return null;
        }

        return [
            'avatar_path' => $user->avatar_path,
            'default_avatar' => $user->default_avatar,
            'unviewed_notifications_count' => $user->unviewedNotificationsCount,
            'unread_conversations_count' => $user->unreadConversationsCount,
        ];
    }
}