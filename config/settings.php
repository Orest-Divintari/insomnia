<?php

use App\User\Privacy;

return [
    'privacy' => [
        'attributes' => [
            'show_current_activity' => true,
            'show_birth_date' => true,
            'show_birth_year' => false,
            'show_details' => Privacy::ALLOW_MEMBERS,
            'post_on_profile' => Privacy::ALLOW_MEMBERS,
            'start_conversation' => Privacy::ALLOW_MEMBERS,
            'show_identities' => Privacy::ALLOW_MEMBERS,
        ],
    ],
    'details' => [
        'attributes' => [
            'location' => '',
            'birth_date' => '',
            'website' => '',
            'gender' => '',
            'occupation' => '',
            'about' => '',
            'skype' => '',
            'google_talk' => '',
            'facebook' => '',
            'twitter' => '',
            'instagram' => '',
        ],
    ],
    'preferences' => [
        'attributes' => [
            'thread_reply_created' => ['database'],
            'thread_reply_quoted' => ['database'],
            'thread_reply_liked' => ['database'],
            'profile_post_created' => ['database'],
            'profile_post_liked' => ['database'],
            'comment_on_a_post_on_your_profile_created' => ['database'],
            'comment_on_your_post_on_your_profile_created' => ['database'],
            'comment_on_your_profile_post_created' => ['database'],
            'comment_on_participated_profile_post_created' => ['database'],
            'comment_liked' => ['database'],
            'message_liked' => ['database'],
            'message_created' => ['mail'],
            'user_followed_you' => ['database'],
            'subscribe_on_creation' => true,
            'subscribe_on_creation_with_email' => true,
            'subscribe_on_interaction' => true,
            'subscribe_on_interaction_with_email' => true,
        ],
    ],
];