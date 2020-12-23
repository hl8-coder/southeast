<?php

$api->get('affiliates/{affiliate}/remarks', 'AffiliatesController@remarks')->name('backstage.affiliates.remarks.index');
$api->post('affiliates/{affiliate}/remarks', 'AffiliatesController@remarksStore')->name('backstage.affiliates.remarks.store');
$api->get('affiliates/{affiliate}/sub_users', 'AffiliatesController@subUsers')->name('backstage.affiliates.sub_users.index');
$api->get('affiliates/{affiliate}/profit_info', 'AffiliatesController@profitInfo')->name('backstage.affiliates.profit_info.index');
$api->get('affiliates/{affiliate}/commissions', 'AffiliatesController@commissions')->name('backstage.affiliates.commissions.index');
$api->get('affiliates/requests', 'AffiliatesController@requestIndex')->name('backstage.affiliates.requests.index');
$api->patch('affiliates/{affiliate}/request_approve', 'AffiliatesController@requestApprove')->name('backstage.affiliates.request.approve');
$api->patch('affiliates/{affiliate}/request_reject', 'AffiliatesController@requestreject')->name('backstage.affiliates.request.reject');
$api->get('affiliates/funds', 'AffiliatesController@fundsIndex')->name('backstage.affiliates.funds.index');
$api->get('affiliates/subs', 'AffiliatesController@subsIndex')->name('backstage.affiliates.subs.index');
$api->get('affiliates/commissions/pending', 'AffiliatesController@pendingCommissions')->name('backstage.affiliates.commissions.pending');
$api->patch('affiliates/commissions/release', 'AffiliatesController@releaseCommissions')->name('backstage.affiliates.commissions.release');
$api->get('affiliates/commissions/pending/download', 'AffiliatesController@downloadPendingCommissions')->name('backstage.affiliates.commissions.pending.download');
$api->get('affiliates/commissions/payout', 'AffiliatesController@payoutCommissions')->name('backstage.affiliates.commissions.payout');
$api->get('affiliates/commissions/payout/download', 'AffiliatesController@downloadPayoutCommissions')->name('backstage.affiliates.commissions.payout.download');
$api->get('affiliates/commissions/formula', 'AffiliatesController@formulaCommissions')->name('backstage.affiliates.commissions.formula');
$api->get('affiliates', 'AffiliatesController@index')->name('backstage.affiliates.index');
$api->post('affiliates', 'AffiliatesController@store')->name('backstage.affiliates.store');
$api->get('affiliates/{affiliate}', 'AffiliatesController@show')->name('backstage.affiliates.show');
# 文档标明使用patch
$api->put('affiliates/{affiliate}', 'AffiliatesController@update')->name('backstage.affiliates.update');
$api->patch('affiliates/{affiliate}', 'AffiliatesController@update')->name('backstage.affiliates.update');

$api->get('tracking_statistic', 'AffiliatesController@creativeReport')->name('backstage.affiliates.tracking_statistic_logs');
$api->get('tracking_statistic/{statistic}', 'AffiliatesController@getInfo')->name('backstage.affiliates.tracking_statistic_logs.detail');
$api->get('affiliates/game_platform_product_details/{user}', 'AffiliatesController@productDetailsBy')->name('backstage.affiliate.game_platform_product_details.index');
$api->get('affiliate/bank/{account}/audit', 'AffiliatesController@affiliateBankAudit')->name('backstage.affiliate.bank.audit');
$api->get('affiliate/bank/history/{affiliate}', 'AffiliatesController@affiliateBankHistory')->name('backstage.affiliate.bank.history');
$api->patch('affiliates/{affiliate}/reset_password', 'AffiliatesController@resetPassword')->name('backstage.affiliate.reset_password');
$api->get('tracking/statistic/logs', 'AffiliatesController@trackingStatisticLogs')->name('backstage.tracking.statistic.logs');

