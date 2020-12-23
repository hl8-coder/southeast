<?php

$api->get('rmtools/user_product_report', 'RmToolsController@userProductReport')->name('backstage.rmtools.user_product_report'); 
$api->get('rmtools/user_product_report_detail', 'RmToolsController@userProductReportDetail')->name('backstage.rmtools.user_product_report_detail'); 
$api->get('rmtools/user_product_report_detail_daily', 'RmToolsController@userProductReportDetailDaily')->name('backstage.rmtools.user_product_report_detail_daily'); 
$api->get('rmtools/user_risk_summary', 'RmToolsController@userRiskSummary')->name('backstage.rmtools.user_risk_summary'); 
$api->get('rmtools/user_product_report/export', 'RmToolsController@userProductReportExport')->name('backstage.rmtools.user_product_report.export'); 
$api->get('member_data_query', 'RmToolsController@memberDataQuery')->name('backstage.member_data_query.index'); 
$api->get('member_data_query/account_summary', 'RmToolsController@getAccountSummary')->name('backstage.account_summary.index'); 
$api->get('member_data_query/account_summary_by_month', 'RmToolsController@accountSummaryByMonth')->name('backstage.account_summary_by_month.index'); 
$api->get('risk_category_listing', 'RmToolsController@riskCategoryListing')->name('backstage.risk_category_listing.index'); 
$api->get('risk_category_listing/{user}', 'RmToolsController@riskCategoryListingShow')->name('backstage.risk_category_listing.show'); 
$api->post('risk_category_listing', 'RmToolsController@storeUserRisk')->name('backstage.risk_category_listing.store'); 


