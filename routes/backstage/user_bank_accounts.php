<?php

$api->get('user_bank_accounts', 'UserBankAccountsController@index')->name('backstage.user_bank_accounts.index'); 
$api->patch('user_bank_accounts/{user_bank_account}/status', 'UserBankAccountsController@updateStatus')->name('backstage.user_bank_accounts.update_status'); 
$api->delete('user_bank_accounts/{user_bank_account}', 'UserBankAccountsController@destroy')->name('backstage.user_bank_accounts.destroy'); 


