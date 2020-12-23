<?php

$api->get('affiliate_announcements', 'AffiliateAnnouncementsController@index')->name('backstage.affiliate_announcements.index');
$api->post('affiliate_announcements', 'AffiliateAnnouncementsController@store')->name('backstage.affiliate_announcements.store');
$api->get('affiliate_announcements/{affiliate_announcement}', 'AffiliateAnnouncementsController@show')->name('backstage.affiliate_announcements.show');
# 这里标示了使用 put ，但是依然保留 patch 方法
$api->put('affiliate_announcements/{affiliate_announcement}', 'AffiliateAnnouncementsController@update')->name('backstage.affiliate_announcements.update');
$api->patch('affiliate_announcements/{affiliate_announcement}', 'AffiliateAnnouncementsController@update')->name('backstage.affiliate_announcements.update');

$api->delete('affiliate_announcements/{affiliate_announcement}', 'AffiliateAnnouncementsController@destroy')->name('backstage.affiliate_announcements.destroy');


