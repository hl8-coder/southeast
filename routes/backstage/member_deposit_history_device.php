<?php

$api->get('member_deposit_history_device', 'ReportsController@memberDepositHistoryDevice')->name('backstage.member_deposit_history_device');
$api->get('member_deposit_history_device/export', 'ReportsController@exportMemberDepositHistoryLog')->name('backstage.member_deposit_history_device.export');