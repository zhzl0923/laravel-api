<?php

$api->version('v1', ['prefix' => '/admin'], function ($api) {
    $api->post('list', 'AdminController@index');
});
