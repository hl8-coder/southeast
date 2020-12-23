<?php

$api->get('bet_to_reward_rules', 'BetToRewardRulesController@index')->name('backstage.bet_to_reward_rules.index'); 
$api->post('bet_to_reward_rules', 'BetToRewardRulesController@store')->name('backstage.bet_to_reward_rules.store'); 
$api->put('bet_to_reward_rules/{bet_to_reward_rule}', 'BetToRewardRulesController@update')->name('backstage.bet_to_reward_rules.update'); 
$api->patch('bet_to_reward_rules/{bet_to_reward_rule}', 'BetToRewardRulesController@update')->name('backstage.bet_to_reward_rules.update'); 


