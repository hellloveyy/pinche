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

Route::get('/', 'UserController@getMyInfo');
Route::get('know', 'HomeController@getHasKnow');
Route::any('history', 'InfoController@getHistoryList');

// 车找人
Route::get('car-find-people', 'HomeController@getCarFindPeople');
// 人找车
Route::get('people-find-car', 'HomeController@getPeopleFindCar');

// 发布车找人
Route::any('create-car', 'InfoController@anyCreateCar');
// 发布人找车
Route::any('create-people', 'InfoController@anyCreatePeople');

// 我的行程(车主)
Route::get('my-trip-car', 'HomeController@getMyTripCar');
// 我的行程(乘车人)
Route::get('my-trip-people', 'HomeController@getMyTripPeople');

// 车主确认已经拼满
Route::get('full-people', 'InfoController@getFullPeople');
// 乘车人确认已经找到车
Route::get('find-car', 'InfoController@getFindCar');