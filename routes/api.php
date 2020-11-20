<?php

use Illuminate\Http\Request;


Route::post('/login', 'Api\AuthController@login');
Route::post('/user/create', 'Api\UserController@create');
