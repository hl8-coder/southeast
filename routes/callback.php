<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

# 前端接口
$api->version('v1', [
    'namespace'     => 'App\Http\Controllers\Callback',
    'middleware'    => ['serializer:array', 'bindings'],
], function($api) {
    $api->group([
        'prefix'    => 'callback',
    ], function($api) {
        $api->any('game_platforms/{code}/login', 'GamePlatformsController@login')->name('callback.game_platforms.login');
    });

});