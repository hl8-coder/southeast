<?php

$api->get('payment_groups', 'PaymentGroupsController@index')->name('backstage.payment_groups.index');
$api->get('payment_groups/audit/{payment_group}', 'PaymentGroupsController@audit')->name('backstage.payment_groups.audit');
$api->post('payment_groups', 'PaymentGroupsController@store')->name('backstage.payment_groups.store');
$api->patch('payment_groups/{payment_group}', 'PaymentGroupsController@update')->name('backstage.payment_groups.update');


