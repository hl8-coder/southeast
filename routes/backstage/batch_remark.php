<?php

$api->get('batch_remark/index', 'BatchRemarkController@index')->name('backstage.batch_remark.index');
$api->get('batch_remark/show/{remark}', 'BatchRemarkController@show')->name('backstage.batch_remark.show');
$api->post('batch_remark/upload', 'BatchRemarkController@uploadFile')->name('backstage.batch_remark.upload');
$api->post('batch_remark/store', 'BatchRemarkController@store')->name('backstage.batch_remark.store');
$api->get('batch_remark/export/template', 'BatchRemarkController@downloadExcel')->name('backstage.batch_remark.export.template');
$api->get('batch_remark/fails/{remark}', 'BatchRemarkController@fails')->name('backstage.batch_remark.fails');



