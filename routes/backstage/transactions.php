<?php

$api->get('user_account_transactions', 'TransactionsController@index')->name('backstage.user_account_transactions.index'); 
$api->get('user_account_transactions/export', 'TransactionsController@exportTransaction')->name('backstage.user_account_transactions.export');


