<?php

$api->get('game_bet_details', 'GameBetDetailsController@index')->name('backstage.game_bet_details.index'); 
$api->get('game_bet_details/excel', 'GameBetDetailsController@exportExcel')->name('backstage.game_bet_details.export_excel'); 


