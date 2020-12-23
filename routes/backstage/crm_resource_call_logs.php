<?php

$api->get('crm_resource_call_logs', 'CrmResourceCallLogsController@index')->name('backstage.crm_resource_call_logs.index'); 
$api->post('crm_resource_call_logs', 'CrmResourceCallLogsController@store')->name('backstage.crm_resource_call_logs.store'); 
$api->get('crm_resources/{crmResource}/crm_resource_call_logs', 'CrmResourceCallLogsController@show')->name('backstage.crm_resource_call_logs.show'); 


