<?php

$api->get('exchange_rates', 'ExchangeRatesController@index')->name('backstage.exchange_rates.index');
$api->post('exchange_rates', 'ExchangeRatesController@store')->name('backstage.exchange_rates.store');
# 文档标注使用 patch，为了安全起见，保留put
$api->put('exchange_rates/{exchange_rate}', 'ExchangeRatesController@update')->name('backstage.exchange_rates.update');
$api->patch('exchange_rates/{exchange_rate}', 'ExchangeRatesController@update')->name('backstage.exchange_rates.update');

$api->delete('exchange_rates/{exchange_rate}', 'ExchangeRatesController@destroy')->name('backstage.exchange_rates.destroy');


