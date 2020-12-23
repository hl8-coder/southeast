<?php

$api->get('crm_orders/crm_call_logs', 'CrmCallLogsController@index')->name('backstage.crm_call_logs.index'); 
$api->post('crm_orders/crm_call_logs', 'CrmCallLogsController@store')->name('backstage.crm_call_logs.store'); 
$api->get('crm_orders/{user}/crm_call_logs', 'CrmCallLogsController@userCrmCallLogs')->name('backstage.crm_call_logs.user'); 
$api->get('crm_orders/{crmOrder}/call_logs', 'CrmCallLogsController@crmOrderCallLogs')->name('backstage.crm_call_logs.show'); 


