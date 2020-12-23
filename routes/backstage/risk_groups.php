<?php

$api->get('risk_groups', 'RiskGroupsController@index')->name('backstage.risk_groups.index');
$api->post('risk_groups', 'RiskGroupsController@store')->name('backstage.risk_groups.store');
# 安全考虑，保留两者
$api->put('risk_groups/{risk_group}', 'RiskGroupsController@update')->name('backstage.risk_groups.update');
$api->patch('risk_groups/{risk_group}', 'RiskGroupsController@update')->name('backstage.risk_groups.update');

$api->delete('risk_groups/{risk_group}', 'RiskGroupsController@destroy')->name('backstage.risk_groups.destroy');
$api->get('risk_groups/audits/{risk_group}', 'RiskGroupsController@audit')->name('backstage.risk_groups.audit');


