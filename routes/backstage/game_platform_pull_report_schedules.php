<?php

$api->get('game_platform_pull_report_schedules', 'GamePlatformPullReportSchedulesController@index')->name('backstage.game_platform_pull_report_schedules.index');
$api->patch('game_platform_pull_report_schedules/{schedule}', 'GamePlatformPullReportSchedulesController@update')->name('backstage.game_platform_pull_report_schedules.update');
$api->get('game_platform_pull_report_schedules_by_platform', 'GamePlatformPullReportSchedulesController@gamePlatformSchedule')->name('backstage.game_platform_pull_report_schedules_by_platform');


