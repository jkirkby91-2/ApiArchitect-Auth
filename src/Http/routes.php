<?php

$this->app->post('/auth/logout', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@logout');
$this->app->post('/auth/login', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@authenticate');

$this->app->group(['middleware' => ['before' => 'psr7adapter', 'after' => 'apiarchitect.auth']], function ($app){
    $this->app->get('/auth/user','ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@user');
});

$this->app->post('/auth/password/reset', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@reset');
$this->app->get('/auth/password/reset/{token}', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@verify');

$this->app->get('/auth/refresh', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@refresh');

$this->app->get('auth/oauth/facebook/redirect', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@redirectToProvider');
$this->app->get('auth/oauth/facebook/callback', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@handleProviderCallback');
