<?php

$api->get('user_login_logs', 'UserLoginLogsController@index')->name('backstage.user_login_logs.index'); 
$api->get('user_login_logs/by_ip', 'UserLoginLogsController@getTheLogsByIp')->name('backstage.user_login_logs.show'); 
$api->get('affiliate_login_logs', 'UserLoginLogsController@affiliateIndex')->name('backstage.affiliate_login_logs.index'); 


