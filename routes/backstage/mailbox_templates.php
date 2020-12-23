<?php

$api->get('mailbox_templates', 'MailboxTemplatesController@index')->name('backstage.mailbox_templates.index'); 
$api->post('mailbox_templates', 'MailboxTemplatesController@store')->name('backstage.mailbox_templates.store'); 
$api->patch('mailbox_templates/{mailbox_template}', 'MailboxTemplatesController@update')->name('backstage.mailbox_templates.update'); 
$api->delete('mailbox_templates/{mailbox_template}', 'MailboxTemplatesController@destroy')->name('backstage.mailbox_templates.destroy'); 


