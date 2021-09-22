<?php

use Illuminate\Support\Facades\Route;



Route::prefix('/admin')->group(function ($route) {
    $route->get('list', 'AdminController@index');
});
