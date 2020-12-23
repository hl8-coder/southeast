<?php

$api->get('crm_tag_setting', 'CrmTagSettingController@index')->name('backstage.crm_tag_setting.index'); 
$api->patch('crm_tag_setting', 'CrmTagSettingController@update')->name('backstage.crm_tag_setting.update'); 


