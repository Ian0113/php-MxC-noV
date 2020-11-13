<?php
use Core\Base\Route;

/**
 * 
 * 格式 :
 * 
 * Route::group
 * Route::middleware
 *    Route::post
 *    Route::put
 *    Route::update
 *    Route::delete
 *    Route::get
 * 
 * 變數 (string 'URI', string 'Controller@function', array Middlewares = [])
 * 
 * 範例 :
 * 1________________________________________
 * Route::get('/exp/test', 'TestController@index');
 * TestController@index 代表在 Controllers 內有 'TestController' 並有function 'index'
 * 
 * 2________________________________________
 * Route::get('/exp/{id}', 'TestController@test');
 * TestController@test 代表在 Controllers 內有 'TestController' 並有function 'test' 並能帶入參數 $id
 * 
 * 3________________________________________
 * Route::get('/exp/test', 'TestController@index', ['TestMiddlewares1', 'TestMiddlewares2']);
 * 代表在執行 TestController@index 之前會先執行 (new TestMiddlewares1)->run() (new TestMiddlewares2)->run()
 * 
 */



Route::group('/api', [

    Route::group('/get', [
        Route::get('/token', 'App\Controllers\TokenController@get'),
        Route::middleware('App\Middlewares\AuthMiddleware', [
            // 需要登入才能訪問
            Route::get('/routelist', 'App\Controllers\HomeController@getRouteList')
        ]),

    ]),

    Route::group('/post', [

        Route::middleware('App\Middlewares\TokenMiddleware', [
            // 需要 token 才能訪問

            Route::middleware('App\Middlewares\AuthMiddleware', [
                // 需要登入才能訪問
                
            ]),

            Route::group('/sign', [
                // 登入 | 註冊 | 登出
                Route::post('In', 'App\Controllers\SignController@signIn', ['App\Middlewares\SignInMiddleware']),
                Route::post('Up', 'App\Controllers\SignController@signUp', ['App\Middlewares\SignUpMiddleware']),
                Route::post('Out', 'App\Controllers\SignController@signOut'),
            ]),

        ]),

    ]),

]);
