<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function ($route) {
    $route->post('login', 'AuthController@login');
});
