<?php
use Illuminate\Http\Request;
Route::post('regmod','nucgController@regmod');
Route::post('gnuc','nucgController@gnuc');
Route::post('mnuc','nucgController@mnuc');
Route::post('hnuc','nucgController@hnuc');
Route::post('cnuc','\fge\nucc\controller\nuccController@cnuc');