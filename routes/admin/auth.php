<?php

$api->version('v1', function ($api) {
    $api->post('/login', 'AuthController@login');
});
