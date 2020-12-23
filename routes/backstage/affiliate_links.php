<?php

$api->get('affiliate_link', 'AffiliateLinksController@index')->name('backstage.affiliate_link.index');
$api->post('affiliate_link', 'AffiliateLinksController@store')->name('backstage.affiliate_link.store');
$api->patch('affiliate_link/{affiliate_link}', 'AffiliateLinksController@update')->name('backstage.affiliate_link.update');


