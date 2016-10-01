<?php

$this->app->post('logout', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@logout');
$this->app->post('login', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@authenticate');
$this->app->post('password/reset', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetController@reset');
$this->app->get('password/reset/{token}', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@verify');

