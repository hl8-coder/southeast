<?php

$api->get('crm_orders', 'CrmOrdersController@index')->name('backstage.crm_orders.index');
$api->patch('crm_orders', 'CrmOrdersController@updateBatch')->name('backstage.crm_orders.update');
$api->patch('crm_orders/{crm_order}/welcome', 'CrmOrdersController@updateWelcomeOrder')->name('backstage.crm_orders.update_welcome_order');
$api->get('crm_orders/excel_report', 'CrmOrdersController@excelReport')->name('backstage.crm_orders.excel_report');
$api->get('crm_orders/crm_order_call_logs', 'CrmOrdersController@crmCallLogs')->name('backstage.crm_orders.crm_call_logs');
$api->get('crm_orders/import_template', 'CrmOrdersController@template')->name('backstage.crm_orders.template');
$api->post('crm_orders', 'CrmOrdersController@store')->name('backstage.crm_orders.store');


