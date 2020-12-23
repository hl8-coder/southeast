<?php
return [
    # subscriberID 所有接口都需要使用subscriberID，更换的时候全部替换
    'subscriberID'       => '949800',
    'subscriberAccount'  => 'OLTP',
    'subscriberPassCode' => 'YV2TRWZPKBSPS6DZ6H8AN4HI5J5I8ARE',
    'product'            => 'http://',
    'local'            => 'https://',
    'url'                => [
        'login'                   => [
            'url'    => 'api.iovation.com/fraud/v1/subs/949800/checks',
            'method' => 'post'
        ],
        'add'                     => [
            'url'    => 'api.iovation.com/fraud/v1/subs/949800/evidence',
            'method' => 'post'
        ],
        'update'                  => [
            'url'    => 'api.iovation.com/fraud/v1/subs/949800/evidence/',
            'method' => 'put'
        ],
        'get'                     => [
            'url'    => 'api.iovation.com/fraud/v1/subs/949800/consortium/evidence',
            'method' => 'get'
        ],
        'retracting_evidence'     => [
            'url'    => 'api.iovation.com/fraud/v1/subs/949800/evidence/{evidenceId}/retractions',
            'method' => 'post'
        ],
        'retracting_all_evidence' => [
            'url'    => 'api.iovation.com/fraud/v1/subs/949800/evidence/retractions',
            'method' => 'post'
        ],
    ]
];
