<?php

$api->get('user_platform_total_reports', 'ReportsController@userPlatformTotalReportIndex')->name('backstage.user_platform_total_reports.index');
$api->get('user_product_reports', 'ReportsController@userProductReportIndex')->name('backstage.user_product_reports.index');
$api->get('user_product_total_reports', 'ReportsController@userProductTotalReportIndex')->name('backstage.user_product_total_reports.index');
$api->get('user_main_wallet_total_report', 'ReportsController@userMainWalletTotalReport')->name('backstage.user_main_wallet_total_report.index');
$api->get('rebate_computation_reports', 'ReportsController@rebateComputationReportIndex')->name('backstage.rebate_computation_reports.index');
$api->get('rebate_computation_reports/user_bonus_prizes', 'ReportsController@getReportUserBonusPrizes')->name('backstage.rebate_computation_reports.user_bonus_prizes');
$api->get('active_user_report', 'ReportsController@activeUserReportIndex')->name('backstage.active_user_report.index');
$api->get('active_user_report_by_affiliate', 'ReportsController@activeUserReportByAffiliateIndex')->name('backstage.active_user_report_by_affiliate.index');
$api->get('active_user_report_by_product', 'ReportsController@activeUserReportByProductIndex')->name('backstage.active_user_report_by_product.index');
# Members Activity Reports
$api->get('member_active_reports', 'ReportsController@memberActiveReports')->name('backstage.member_active_reports');
