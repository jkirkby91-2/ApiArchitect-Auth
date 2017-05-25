<?php

$this->app->group(['middleware' => ['before' => 'psr7adapter', 'after' => 'apiarchitect.auth']], function ($app){
    $this->app->get('/auth/user','ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@user');
});

$this->app->group(['middleware' => ['before' => 'psr7adapter', 'after' => 'apiarchitect.auth']], function ($app){
    resource('user','ApiArchitect\Auth\Http\Controllers\User\UserController');
});

$this->app->post('/auth/register', 'ApiArchitect\Auth\Http\Controllers\UserController@register');
$this->app->post('/auth/login', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@authenticate');
$this->app->post('/auth/logout', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@logout');


$this->app->post('/auth/password/reset', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@reset');
$this->app->get('/auth/password/reset/{token}', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@verify');

$this->app->get('/auth/refresh', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@refresh');

$this->app->get('auth/oauth/{provider}/redirect', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@redirectToProvider');
$this->app->post('auth/oauth/{provider}/callback', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@handleProviderCallback');

$this->app->post('auth/check/username', 'ApiArchitect\Auth\Controllers\UserController@checkUniqueUserName');
$this->app->post('auth/check/email', 'ApiArchitect\Auth\Controllers\UserController@checkUniqueEmail');
