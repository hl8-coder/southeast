<?php

$api->get('adjustments', 'AdjustmentsController@index')->name('backstage.adjustments.index');
$api->get('adjustment/export', 'AdjustmentsController@adjustmentExport')->name('backstage.adjustments.export');
$api->post('users/{user_name}/adjustments', 'AdjustmentsController@store')->name('backstage.adjustments.store');
$api->delete('adjustments/{adjustment}', 'AdjustmentsController@reject')->name('backstage.adjustments.reject');
$api->delete('adjustments/{adjustment}/close', 'AdjustmentsController@close')->name('backstage.adjustments.close');
$api->patch('adjustments/{adjustment}/approve', 'AdjustmentsController@approve')->name('backstage.adjustments.approve');


