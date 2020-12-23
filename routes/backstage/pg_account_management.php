<?php

# pg account list
$api->get('pg_account_management', 'PgAccountsController@index')->name('backstage.pg_account_management.index');

# pg account reports
$api->get('pg_account_management/reports', 'PgAccountsController@reportIndex')->name('backstage.pg_account_management.reports.index');

# pg account detail
$api->get('pg_account_management/{pg_account}', 'PgAccountsController@show')->name('backstage.pg_account_management.show');

# pg account adjust.
$api->PATCH('pg_account_management/adjust', 'PgAccountsController@adjust')->name('backstage.pg_account_management.adjust');

#pg account internalTransfer
$api->PATCH('pg_account_management/internal_transfer', 'PgAccountsController@internalTransfer')->name('backstage.pg_account_management.internalTransfer');

# pg account do remark
$api->post('pg_account_management/{pg_account}/remark', 'PgAccountsController@remark')->name('backstage.pg_account_management.remark');

# pg account remark list
$api->get('pg_account_management/{pg_account}/remarks', 'PgAccountsController@remarkIndex')->name('backstage.pg_account_management.remarks.index');

$api->patch('pg_account_management/{pg_account}', 'PgAccountsController@update')->name('backstage.pg_account_management.update');






