<?php

$this->app->post('login', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@authenticate');
$this->app->post('logout', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@logout');
$this->app->post('register', 'ApiArchitect\Compass\Http\Controllers\User\UserController@register');
//$this->app->post('password/reset', 'ApiArchitect\Compass\Http\Controllers\User\UserController@register');

