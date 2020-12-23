<?php

$api->get('user_risk_group/template', 'UserRiskGroupController@template')->name('backstage.user_risk_groups.template');
$api->post('user_risk_group', 'UserRiskGroupController@store')->name('backstage.user_risk_groups.store');


