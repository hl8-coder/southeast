<?php

$api->get('pg_account_transactions', 'PgAccountTransactionsController@index')->name('backstage.pg_account_transactions.index');

$api->get('pg_account_transactions/export', 'PgAccountTransactionsController@exportPgAccountTransaction')->name('backstage.pg_account_transactions.export');
