<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace'  => 'App\Http\Controllers\Backstage',
    'middleware' => ['serializer:array', 'bindings'],
], function ($api) {
    $api->group([
        'prefix' => 'backstage',
    ], function ($api) {
        # 登录
        $api->post('authorizations', 'AuthorizationsController@store')->name('backstage.authorizations.store');
        # 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')->name('backstage.authorizations.update');
        # 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')->name('backstage.authorizations.destroy');
        # 支付回调
        $api->any('deposits/call_back/{code}', 'DepositsController@callBack')->name('backstage.deposits.call_back');
        # 平台名称
        $api->get('get/operation_id', 'ConfigsController@getOperationId')->name('backstage.configs.get.operation_id');
    });

    $api->group([
        # , 'auth:bo'
        'middleware' => ['auth:admin', 'check-route-permission', 'backstage-log'],
        'prefix'     => 'backstage',

        # 这里闭包
    ], function ($api) {
        $backstageRoutePath = base_path('routes/backstage');
        $routeFiles         = \Illuminate\Support\Facades\File::allFiles($backstageRoutePath);
        foreach ($routeFiles as $file) {
            require $file;
        }
    });
});
