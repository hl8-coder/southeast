<?php

$api->get('crm_resources/excel_template', 'CrmResourcesController@excelTemplate')->name('backstage.crm_resources.excel_template'); 
$api->get('crm_resources', 'CrmResourcesController@index')->name('backstage.crm_resources.index'); 
$api->patch('crm_resources', 'CrmResourcesController@update')->name('backstage.crm_resources.update'); 
$api->post('crm_resources', 'CrmResourcesController@store')->name('backstage.crm_resources.store'); 


