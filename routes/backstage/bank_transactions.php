<?php

$api->get('bank_transactions', 'BankTransactionsController@index')->name('backstage.bank_transactions.index'); 
$api->post('bank_transactions/excel', 'BankTransactionsController@importExcel')->name('backstage.bank_transactions.excel'); 
$api->post('bank_transactions/text', 'BankTransactionsController@importText')->name('backstage.bank_transactions.text'); 
$api->patch('bank_transactions/{bank_transaction}/credit', 'BankTransactionsController@updateCredit')->name('backstage.bank_transactions.credit'); 
$api->get('bank_transactions/duplicate_transactions', 'BankTransactionsController@getDuplicateTransactions')->name('backstage.bank_transactions.duplicate_transactions'); 
$api->delete('bank_transactions/duplicate_transactions', 'BankTransactionsController@destroyDuplicateTransactions')->name('backstage.bank_transactions.destroy_duplicate_transactions'); 
$api->get('bank_transactions/{bank_transaction}', 'BankTransactionsController@show')->name('backstage.bank_transactions.show'); 
$api->delete('bank_transactions/{bank_transaction}', 'BankTransactionsController@destroy')->name('backstage.bank_transactions.destroy'); 
$api->get('bank_transactions/{bank_transaction}/audit', 'BankTransactionsController@audit')->name('backstage.bank_transactions.audit'); 


