<?php

$api->get('promotion_claim_users', 'PromotionClaimUsersController@index')->name('backstage.promotion_claim_users.index'); 
$api->patch('promotion_claim_users/{promotion_claim_user}/status', 'PromotionClaimUsersController@updateStatus')->name('backstage.promotion_claim_users.status'); 


