<?php

$api->get('users/{user_name}/game_platform_transfer_details', 'GamePlatformTransferDetailsController@userIndex')->name('backstage.users.game_platform_transfer_details.index'); 
$api->get('game_platform_transfer_details', 'GamePlatformTransferDetailsController@index')->name('backstage.game_platform_transfer_details.index'); 
$api->patch('game_platform_transfer_details/{game_platform_transfer_detail}/manual_success', 'GamePlatformTransferDetailsController@manualSuccess')->name('backstage.game_platform_transfer_details.manual_success'); 
$api->patch('game_platform_transfer_details/{game_platform_transfer_detail}/manual_fail', 'GamePlatformTransferDetailsController@manualFail')->name('backstage.game_platform_transfer_details.manual_fail'); 
$api->patch('game_platform_transfer_details/{game_platform_transfer_detail}/add_check_job', 'GamePlatformTransferDetailsController@addCheckJob')->name('backstage.game_platform_transfer_details.add_check_job');


