<?php

$hl8 = [
    'USD' => [
        'Member' => [
            'verify' => [
                'username' => 'verification@hl8.com',
                'password' => 'TyAGXMPDw2GjMzTDTV9VVV2uDzv7mnMA',
            ],
            'welcome' => [
                'username' => 'csvn@hl8viet.com',
                'password' => 'pCzHGZvjzrae4LXRCzvZMkta6mWkCLLg',
            ],
        ],
        'Affiliate' => [
            'username' => 'Affiliate@HL8viet.com',
            'password' => 'affiliate!!!',
        ],
    ],
    'CNY' => [
        'Member' => [
            'verify' => [
                'username' => 'verification@hl8.com',
                'password' => 'TyAGXMPDw2GjMzTDTV9VVV2uDzv7mnMA',
            ],
            'welcome' => [
                'username' => 'csvn@hl8viet.com',
                'password' => 'pCzHGZvjzrae4LXRCzvZMkta6mWkCLLg',
            ],
        ],
        'Affiliate' => [
            'username' => 'Affiliate@HL8viet.com',
            'password' => 'affiliate!!!',
        ],
    ],
    'VND' => [
        'Member' => [
            'verify' => [
                'username' => 'verification@hl8.com',
                'password' => 'TyAGXMPDw2GjMzTDTV9VVV2uDzv7mnMA',
            ],
            'welcome' => [
                'username' => 'csvn@hl8viet.com',
                'password' => 'pCzHGZvjzrae4LXRCzvZMkta6mWkCLLg',
            ],
        ],
        'Affiliate' => [
            'username' => 'Affiliate@HL8viet.com',
            'password' => 'affiliate!!!',
        ],
    ],
    'THB' => [
        'Member' => [
            'verify' => [
                'username' => 'verification@hl8.com',
                'password' => 'TyAGXMPDw2GjMzTDTV9VVV2uDzv7mnMA',
            ],
            'welcome' => [
                'username' => 'csthai@hl8.com',
                'password' => 'hl8@1234',
            ],
        ],
        'Affiliate' => [
            'username' => 'affiliateth@hl8.com',
            'password' => 'cHrrmgLDmwQaWHmRLyp9cRFzR5hXC4DV',
        ],
    ],
];

$eg = [
    'Member' => [
        'verify' => [
            'username' => 'verification@empiregem.com',
            'password' => '8jjWa3eVuFkLLNzqWSVQhE2ZqvU6GreS',
        ],
        'welcome' => [
            'username' => 'egsupport@empiregem.com',
            'password' => '2cTcFSGndb5tkyH9D4GhQ64YTSAgfM6u',
        ],
    ],
    'Affiliate' => [
        'username' => 'affiliate@empiregem.com',
        'password' => 'affiliateth1234',
    ]
];

# EG.TH.BE & HL8.VN.BE

if (env('APP_NAME') == 'EG.TH.BE') {
    return $eg;
} else {
    return $hl8;
}