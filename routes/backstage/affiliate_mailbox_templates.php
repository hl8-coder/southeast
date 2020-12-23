<?php

$api->get('affiliate/mailbox_templates', 'AffiliateMailboxTemplatesController@index')->name('backstage.affiliate.mailbox_templates.index'); 
$api->post('affiliate/mailbox_templates', 'AffiliateMailboxTemplatesController@store')->name('backstage.affiliate.mailbox_templates.store'); 


