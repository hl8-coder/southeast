<?php

$api->get('batch_adjustment/index', 'BatchAdjustmentController@index')->name('backstage.batch_adjustment.index'); 
$api->get('batch_adjustment/show/{adjustment}', 'BatchAdjustmentController@show')->name('backstage.batch_adjustment.show'); 
$api->post('batch_adjustment/upload', 'BatchAdjustmentController@uploadFile')->name('backstage.batch_adjustment.upload'); 
$api->post('batch_adjustment/store', 'BatchAdjustmentController@store')->name('backstage.batch_adjustment.store'); 
$api->get('batch_adjustment/export/template', 'BatchAdjustmentController@downloadExcel')->name('backstage.batch_adjustment.export.template'); 


