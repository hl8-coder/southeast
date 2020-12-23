<?php

$api->get('remarks', 'RemarksController@index')->name('backstage.remarks.index'); 
$api->post('remarks', 'RemarksController@store')->name('backstage.remarks.store'); 
$api->get('remarks/{remark}', 'RemarksController@show')->name('backstage.remarks.show'); 
$api->delete('remarks/{remark}', 'RemarksController@destroy')->name('backstage.remarks.destroy'); 
$api->post('remarks/by/username', 'RemarksController@storeRemarkByUsername')->name('backstage.remarks.by_user_name.store'); 


