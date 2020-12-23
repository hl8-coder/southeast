<?php


$api->get('kpi_report', 'KpiReportsController@index')->name('backstage.kpi_report.index');
$api->get('kpi_report/excel_report', 'KpiReportsController@excelReport')->name('backstage.kpi_report.excel_report');

