<?php

$api->get('routes', 'RoutesController@index')->name('backstage.routes.index');
$api->get('routes/{route}', 'RoutesController@show')->name('backstage.routes.show');
$api->get('routes/list', 'RoutesController@routeList')->name('backstage.routes.routeList');
$api->patch('routes/update', 'RoutesController@routeUpdate')->name('backstage.routes.routeUpdate');


