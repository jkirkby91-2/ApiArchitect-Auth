<?php

$this->app->group(['middleware' => ['before' => 'psr7adapter', 'after' => 'apiarchitect.auth']], function ($app){
    $this->app->get('/auth/user','ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@user');
});

$this->app->group(['middleware' => ['before' => 'psr7adapter', 'after' => 'apiarchitect.auth']], function ($app){
	$this->app->get('/user', 'ApiArchitect\Auth\Http\Controllers\UserController@index');
	$this->app->get('/user/{id}', 'ApiArchitect\Auth\Http\Controllers\UserController@show');
	$this->app->post('/user', 'ApiArchitect\Auth\Http\Controllers\UserController@store');
	$this->app->put('/user/{id}', 'ApiArchitect\Auth\Http\Controllers\UserController@update');
	$this->app->delete('/user/{id}','ApiArchitect\Auth\Http\Controllers\UserController@destroy');
});

$this->app->group(['middleware' => ['before' => 'psr7adapter', 'after' => 'validateRequest:ApiArchitect\Auth\Http\Requests\UserRegistrationRequest']], function ($app)
{
  $this->app->post('/auth/register', 'ApiArchitect\Auth\Http\Controllers\UserController@register');
});
$this->app->post('/auth/login', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@authenticate');
$this->app->post('/auth/logout', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@logout');


$this->app->post('/auth/password/reset', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@reset');
$this->app->get('/auth/password/reset/{token}', 'ApiArchitect\Auth\Http\Controllers\Auth\PasswordResetsController@verify');

$this->app->get('/auth/refresh', 'ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController@refresh');

$this->app->get('auth/oauth/{provider}/redirect', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@redirectToProvider');
$this->app->post('auth/oauth/{provider}/callback', 'ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController@handleProviderCallback');

$this->app->post('auth/check/username', 'ApiArchitect\Auth\Controllers\UserController@checkUniqueUserName');
$this->app->post('auth/check/email', 'ApiArchitect\Auth\Controllers\UserController@checkUniqueEmail');
