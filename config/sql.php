<?php

return [

    # 是否写日志到 Log
    'sql_log' => env('SQL_LOG', false),

    # Log 日志写在那个频道里
    'log_channel' => 'sql_log',
];
