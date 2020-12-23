<?php

$api->get('currencies', 'CurrenciesController@index')->name('backstage.currencies.index');
$api->post('currencies', 'CurrenciesController@store')->name('backstage.currencies.store');
$api->patch('currencies/{currency}', 'CurrenciesController@update')->name('backstage.currencies.update');
$api->delete('currencies/{currency}', 'CurrenciesController@destroy')->name('backstage.currencies.destroy');


