<?php

$api->get('company_bank_accounts', 'CompanyBankAccountsController@index')->name('backstage.company_bank_accounts.index'); 
$api->post('company_bank_accounts', 'CompanyBankAccountsController@store')->name('backstage.company_bank_accounts.store'); 
$api->get('company_bank_accounts/reports', 'CompanyBankAccountsController@reportIndex')->name('backstage.company_bank_accounts.reports.index'); 
$api->patch('company_bank_accounts/adjust', 'CompanyBankAccountsController@adjust')->name('backstage.company_bank_accounts.adjust'); 
$api->patch('company_bank_accounts/buffer_transfer', 'CompanyBankAccountsController@bufferTransfer')->name('backstage.company_bank_accounts.buffer_transfer'); 
$api->patch('company_bank_accounts/internal_transfer', 'CompanyBankAccountsController@internalTransfer')->name('backstage.company_bank_accounts.internal_transfer');
$api->get('company_bank_accounts/code', 'CompanyBankAccountsController@showByCode')->name('backstage.company_bank_accounts.code.show');
$api->get('company_bank_accounts/{company_bank_account}', 'CompanyBankAccountsController@show')->name('backstage.company_bank_accounts.show');
$api->patch('company_bank_accounts/{company_bank_account}', 'CompanyBankAccountsController@update')->name('backstage.company_bank_accounts.update');
$api->get('company_bank_accounts/{company_bank_account}/audits', 'CompanyBankAccountsController@audits')->name('backstage.company_bank_accounts.audits'); 
$api->post('company_bank_accounts/{company_bank_account}/remarks', 'CompanyBankAccountsController@remark')->name('backstage.company_bank_accounts.remarks'); 
$api->get('company_bank_accounts/{company_bank_account}/remarks', 'CompanyBankAccountsController@remarkIndex')->name('backstage.company_bank_accounts.remarks.index'); 


