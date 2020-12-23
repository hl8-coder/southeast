<?php

$api->get('crm_bo_admins', 'CrmBoAdminsController@index')->name('backstage.crm_bo_admins.index');
$api->post('crm_bo_admins', 'CrmBoAdminsController@store')->name('backstage.crm_bo_admins.store');
$api->patch('crm_bo_admins/{crm_bo_admin}', 'CrmBoAdminsController@update')->name('backstage.crm_bo_admins.update');
$api->delete('crm_bo_admins/{crm_bo_admin}', 'CrmBoAdminsController@destroy')->name('backstage.crm_bo_admins.destroy');
$api->get('crm_bo_admins/audits', 'CrmBoAdminsController@audits')->name('backstage.crm_bo_admins.audits');


