<?php
# 在引入主文件已经存在
// $api->get('deposits/call_back/{code}', 'DepositsController@callBack')->name('backstage.deposits.call_back');
// $api->post('deposits/call_back/{code}', 'DepositsController@callBack')->name('backstage.deposits.call_back');
// $api->put('deposits/call_back/{code}', 'DepositsController@callBack')->name('backstage.deposits.call_back');
// $api->patch('deposits/call_back/{code}', 'DepositsController@callBack')->name('backstage.deposits.call_back');
// $api->delete('deposits/call_back/{code}', 'DepositsController@callBack')->name('backstage.deposits.call_back');

$api->get('users/{user_name}/deposits', 'DepositsController@byUser')->name('backstage.users.deposits.index');
$api->get('deposits', 'DepositsController@index')->name('backstage.deposits.index');
$api->get('deposits/open_deposit', 'DepositsController@openDeposit')->name('backstage.deposits.open_deposit.index');
$api->get('deposits/fast_deposit', 'DepositsController@fastDeposit')->name('backstage.deposits.fast_deposit.index');
$api->get('deposits/gateway', 'DepositsController@gateway')->name('backstage.deposits.gateway.index');
$api->get('deposits/advance_credit', 'DepositsController@advanceCredit')->name('backstage.deposits.advance_credit.index');
$api->get('deposits/{deposit}/logs', 'DepositsController@logIndex')->name('backstage.deposits.logs');
$api->patch('deposits/{deposit}/amount_detail', 'DepositsController@updateAmountDetail')->name('backstage.deposits.amount_detail.update');
$api->patch('deposits/{deposit}/remarks', 'DepositsController@updateRemarks')->name('backstage.deposits.remarks.update');
$api->post('deposits/{deposit}/receipt', 'DepositsController@receipt')->name('backstage.deposits.receipt');
$api->delete('deposits/{deposit}/receipt/{image_id}', 'DepositsController@receiptDelete')->name('backstage.deposits.receipt.delete');
$api->patch('deposits/{deposit}/hold', 'DepositsController@hold')->name('backstage.deposits.hold');
$api->patch('deposits/{deposit}/release_hold', 'DepositsController@releaseHold')->name('backstage.deposits.release_hold');
$api->patch('deposits/{deposit}/approve', 'DepositsController@approve')->name('backstage.deposits.approve');
$api->patch('deposits/{deposit}/approve_changes', 'DepositsController@approveChanges')->name('backstage.deposits.approve_changes');
$api->patch('deposits/{deposit}/request_advance', 'DepositsController@requestAdvance')->name('backstage.deposits.request_advance');
$api->patch('deposits/{deposit}/approve_adv', 'DepositsController@approveAdv')->name('backstage.deposits.approve_adv');
$api->patch('deposits/{deposit}/approve_partial', 'DepositsController@approvePartial')->name('backstage.deposits.approve_partial');
$api->patch('deposits/{deposit}/revert_action', 'DepositsController@revertAction')->name('backstage.deposits.revert_action');
$api->patch('deposits/{deposit}/approve_advance_credit', 'DepositsController@approveAdvanceCredit')->name('backstage.deposits.approve_advance_credit');
$api->patch('deposits/{deposit}/approve_partial_advance_credit', 'DepositsController@approvePartialAdvanceCredit')->name('backstage.deposits.approve_partial_advance_credit');
$api->patch('deposits/{deposit}/reject', 'DepositsController@reject')->name('backstage.deposits.reject');
$api->patch('deposits/{deposit}/cancel', 'DepositsController@cancel')->name('backstage.deposits.cancel');
$api->patch('deposits/{deposit}/lose', 'DepositsController@lose')->name('backstage.deposits.lose');
$api->patch('deposits/{deposit}/match/bank_transactions/{bank_transaction}', 'DepositsController@match')->name('backstage.deposits.match.bank_transaction');
$api->patch('deposits/{deposit}/unmatch', 'DepositsController@unmatch')->name('backstage.deposits.unmatch.bank_transaction');
$api->patch('deposits/{deposit}/final_approve', 'DepositsController@finalApprove')->name('backstage.deposits.final_approve');
$api->get('users/{user}/deposits/remarks', 'DepositsController@remarkIndex')->name('backstage.users.deposits.remarks.index');
$api->get('deposits/bank_transactions/{order_no}', 'DepositsController@bankTransaction')->name('backstage.deposits.bank_transactions.show');
$api->get('deposits/{deposit}', 'DepositsController@show')->name('backstage.deposit.show');
$api->get('export/deposit/log', 'DepositsController@exportDepositLogs')->name('backstage.export.deposit.log');


