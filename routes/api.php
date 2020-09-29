<?php

use Illuminate\Http\Request;

Route::post('/login', 'API\AuthController@login');