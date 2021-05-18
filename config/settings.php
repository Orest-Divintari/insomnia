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
];