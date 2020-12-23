<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

$config = [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver'            => 'stack',
            'channels'          => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],

        'daily' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],

        'slack' => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => 'critical',
        ],

        'papertrail' => [
            'driver'       => 'monolog',
            'level'        => 'debug',
            'handler'      => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver'    => 'monolog',
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with'      => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level'  => 'debug',
        ],

        'errorlog'      => [
            'driver' => 'errorlog',
            'level'  => 'debug',
        ],
        'api_log'       => [
            'driver' => 'daily',
            'path'   => storage_path('logs') . '/api/api.log',
            'level'  => 'info',
            'days'   => 7,
        ],
        'backstage_log' => [
            'driver' => 'daily',
            'path'   => storage_path('logs') . '/backstage/backstage.log',
            'level'  => 'info',
            'days'   => 360,
        ],
        'email_test'    => [
            'driver' => 'daily',
            'path'   => storage_path('logs/email_test.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],

        'deposit_log' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/deposit/deposit.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],

        'http_login'  => [
            'driver' => 'daily',
            'path'   => storage_path('logs/http_login.log'),
            'level'  => 'debug',
            'days'   => 35,
        ],

        'sql_log'     => [
            'driver' => 'daily',
            'path'   => storage_path('logs/sql.log'),
            'level'  => 'debug',
            'days'   => 2,
        ],

        'crm_log'     => [
            'driver' => 'daily',
            'path'   => storage_path('logs/crm.log'),
            'level'  => 'debug',
            'days'   => 10,
        ],

        'command_mark_log'     => [
            'driver' => 'daily',
            'path'   => storage_path('logs/command_mark.log'),
            'level'  => 'debug',
            'days'   => 10,
        ],

        'command_migrate_game_data'     => [
            'driver' => 'daily',
            'path'   => storage_path('logs/command_migrate_game_data.log'),
            'level'  => 'info',
            'days'   => 10,
        ],

        'kpi' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/kpi_report.log'),
            'level'  => 'debug',
            'days'   => 10,
        ],
    ],

];

# command
$commands = [
    'add_transaction_to_process',
    'add_game_bet_detail_to_process',
    'game_platform_report_schedules',
    'update_game_list',
    'calculate_rebates',
    'calculate-affiliate-commission',
    'generate-crm-retention-command',
    'auto-distribution-crm',
];

foreach ($commands as $command) {
    $config['channels'][$command] = [
        'driver' => 'daily',
        'path'   => storage_path('logs') . '/' . $command . '/command.log',
        'level'  => 'info',
        'days'   => 7,
    ];
}

#job
$jobs = [
    'transaction_process',
    'check_wait_transfer_detail',
    'do_withdraw',
    'send_message',
    'send_email',
    'auto_deposit',
];

foreach ($jobs as $job) {
    $config['channels'][$job] = [
        'driver' => 'daily',
        'path'   => storage_path('logs') . '/' . $job . '/job.log',
        'level'  => 'info',
        'days'   => 7,
    ];
}

# 第三方游戏
$gamePlatforms = ['rtg', 'sa', 'ibc', 'ebet', 'smartsoft', 'sas', 'isb', 'sp', 'n2', 'gpi', 's128', 'gg','b46', 'mgs', 'sbo', 'imsports', 'imesports', 'pt','pp','ss'];

foreach ($gamePlatforms as $platform) {
    $config['channels'][$platform] = [
        'driver' => 'daily',
        'path'   => storage_path('logs') . '/' . $platform . '/call.log',
        'level'  => 'info',
        'days'   => 7,
    ];
}

# 短信/邮件服务
$services = ['fly_one_talk', 'send_pulse', 'nexmo', 'email'];

foreach ($services as $service) {
    $config['channels'][$service] = [
        'driver' => 'daily',
        'path'   => storage_path('logs') . '/' . $service . '/send.log',
        'level'  => 'info',
        'days'   => 7,
    ];
}

# Fraud Force
$fraudForce = ['fraud_force'];
foreach ($fraudForce as $value) {
    $config['channels'][$value] = [
        'driver' => 'daily',
        'path'   => storage_path('logs') . '/' . $value . '.log',
        'level'  => 'info',
        'days'   => 7,
    ];
}
return $config;
