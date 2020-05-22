<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->group(['prefix' => 'api'], function () use ($router) {
    
    	#for test
        $router->get('/test', 'FamilyMembershipController@getOwnerMembership');
        $router->get('/test2', 'FamilyMembershipController@checkCurrentRole');

        #routes
        $router->post('/invite', 'FamilyMembershipController@giveMembershipToTeam');
        $router->post('/change-role', 'FamilyMembershipController@changeRole');
        $router->post('/remove', 'FamilyMembershipController@deleteMember');
    });
});
