<?php

$api->get('user_bonus_prizes/user_index', 'UserBonusPrizesController@userIndex')->name('backstage.user_bonus_prizes.user_index'); 
$api->get('user_bonus_prizes/user_index/export', 'UserBonusPrizesController@exportUserIndex')->name('backstage.users.user_bonus_prizes.export');
$api->get('user_bonus_prizes/promotioncheckingtool/export', 'UserBonusPrizesController@exportPromotionCheckingTool')->name('backstage.users.user_bonus_prizes.promotioncheckingtool.export');
$api->get('user_bonus_prizes/report_index', 'UserBonusPrizesController@reportIndex')->name('backstage.user_bonus_prizes.report_index');
$api->delete('user_bonus_prizes/{user_bonus_prize}', 'UserBonusPrizesController@close')->name('backstage.user_bonus_prizes.close'); 


