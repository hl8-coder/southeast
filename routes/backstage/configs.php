<?php
# 在引入主文件已经存在
// $api->get('get/operation_id', 'ConfigsController@getOperationId')->name('backstage.configs.get.operation_id');

$api->get('configs', 'ConfigsController@index')->name('backstage.configs.index');
$api->put('configs/{config}', 'ConfigsController@update')->name('backstage.configs.update');
$api->patch('configs/{config}', 'ConfigsController@update')->name('backstage.configs.update');


