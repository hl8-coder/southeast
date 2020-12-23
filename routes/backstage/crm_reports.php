<?php

$api->get('crm_report/weekly', 'CrmReportsController@weeklyReport')->name('crm_report.weekly'); 
$api->get('crm_report/daily', 'CrmReportsController@dailyReport')->name('crm_report.daily'); 
$api->get('crm_report/weekly/export_excel', 'CrmReportsController@weeklyReportExcelExport')->name('crm_report.weekly.excel'); 
$api->get('crm_report/daily/export_excel', 'CrmReportsController@dailyReportExcelExport')->name('crm_report.daily.excel'); 


