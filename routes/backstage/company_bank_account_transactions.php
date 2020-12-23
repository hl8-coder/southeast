<?php

$api->get('company_bank_account_transactions', 'CompanyBankAccountTransactionsController@index')->name('backstage.company_bank_account_transactions.index');

$api->get('company_bank_account_transactions/export', 'CompanyBankAccountTransactionsController@exportBankAccountManagement')->name('backstage.company_bank_account_transactions.export');
