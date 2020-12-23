<?php

$api->get('users', 'UsersController@index')->name('backstage.users.index');
$api->post('users', 'UsersController@store')->name('backstage.users.store');
$api->get('users/{user}', 'UsersController@show')->name('backstage.users.show');
$api->put('users/{user}', 'UsersController@update')->name('backstage.users.update');
$api->patch('users/{user}', 'UsersController@update')->name('backstage.users.update');
$api->get('user/get_user_by_name', 'UsersController@showUserByName')->name('backstage.users.get_user_by_name');
$api->patch('users/{user}/reset_password', 'UsersController@resetPassword')->name('backstage.users.reset_password');
$api->patch('users/{user}/status', 'UsersController@updateStatus')->name('backstage.users.update_status');
$api->patch('users/{user}/risk_group', 'UsersController@updateRiskGroup')->name('backstage.users.update_risk_group');
$api->patch('users/{user}/payment_group', 'UsersController@updatePaymentGroup')->name('backstage.users.update_payment_group');
$api->patch('users/{user}/reward', 'UsersController@updateReward')->name('backstage.users.update_reward');
$api->patch('users/{user}/reset_security_question', 'UsersController@resetSecurityQuestion')->name('backstage.users.reset_security_question');
$api->patch('game_platform_users/{game_platform_user}/balance_status', 'UsersController@updateGameWalletStatus')->name('backstage.game_platform_user.balance_status');
$api->patch('users/{user}/verify_phone', 'UsersController@verifyPhone')->name('backstage.users.verify_phone');
$api->patch('users/{user}/verify_email', 'UsersController@verifyEmail')->name('backstage.users.verify_email');
$api->patch('users/{user}/verify_bank_account', 'UsersController@verifyBankAccount')->name('backstage.users.verify_bank_account');
$api->get('users/{user}/audit', 'UsersController@audit')->name('backstage.users.audit');
$api->post('users/{user}/claim_verify_prize', 'UsersController@claimVerifyPrize')->name('backstage.users.claim_verify_prize');


