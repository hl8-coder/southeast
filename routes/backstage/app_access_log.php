<?php
$api->get('app_access_log', 'ReportsController@appAccessLog')->name('backstage.app_access_log');
$api->get('app_access_log/export', 'ReportsController@exportAppAccessLog')->name('backstage.app_access_log.export');
