<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');

Route::any('info', 'InfoController@anyCreateInfo');
Route::any('withdraw', 'InfoController@getWithdraw');
Route::any('tempinfo', 'InfoController@anyCreateInfoFormTemp');
Route::any('history', 'InfoController@getHistoryList');


Route::any('request', 'InfoController@getRequest'); // 点击申请
Route::any('request-list', 'InfoController@getRequestList');  // 信息关联的申请列表
Route::any('approve', 'InfoController@getApprove'); // 点击同意申请
Route::any('reject', 'InfoController@getReject'); // 点击驳回申请
Route::any('my-released', 'InfoController@getMyReleased'); // 我的发布列表
Route::any('my-request', 'InfoController@getMyRequest'); // 我的申请
Route::any('detail', 'InfoController@getDetail'); // 车主详细信息


