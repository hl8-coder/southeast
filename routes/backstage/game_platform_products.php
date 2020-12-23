<?php

$api->get('game_platform_products', 'GamePlatformProductsController@index')->name('backstage.game_platform_products.index'); 
$api->get('game_platform_products/relation', 'GamePlatformProductsController@relation')->name('backstage.game_platform_products.relation'); 
$api->patch('game_platform_products/{game_platform_product}', 'GamePlatformProductsController@update')->name('backstage.game_platform_products.update'); 


