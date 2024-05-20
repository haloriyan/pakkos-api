<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "admin"], function () {
    Route::post('store', "AdminController@store");
    Route::post('delete', "AdminController@delete");
    Route::post('update', "AdminController@update");
    Route::post('login', "AdminController@login");

    Route::get('/', "AdminController@admin");
});

Route::group(['prefix' => "city"], function () {
    Route::post('create', "CityController@create");
    Route::post('delete', "CityController@delete");
    Route::post('priority', "CityController@priority");
    Route::post('default', "CityController@default");
    Route::post('update', "CityController@update");
    Route::get('{id?}', "CityController@get");
});

Route::group(['prefix' => "facility"], function () {
    Route::post('create', "FacilityController@create");
    Route::post('delete', "FacilityController@delete");
    Route::post('featured', "FacilityController@featured");

    Route::get('featured', "FacilityController@getFeatured");
    Route::get('/', "FacilityController@get");
});

Route::group(['prefix' => "template"], function () {
    Route::post('create', "TemplateController@create");
    Route::post('delete', "TemplateController@delete");
    Route::get('/', "TemplateController@get");
});

Route::group(['prefix' => "user"], function () {
    Route::post('google-profile', "UserController@googleProfile");
    Route::post('request-to-be-host', "UserController@requestToBeHost");
    Route::post('login', "UserController@login");
    Route::post('login-email', "UserController@loginEmail");
    Route::post('auth', "UserController@auth");
    Route::post('register', "UserController@register");

    Route::post('forget-password', "UserController@forgetPassword");
    Route::get('reset-password/{token}', "UserController@resetPasswordToken");
    Route::post('reset-password', "UserController@resetPassword");

    Route::post('wishlist', "WishlistController@get");
    Route::post('wishlist/put', "WishlistController@put");

    Route::post('action/{name}', "AdminController@userAction");

    Route::get('/', "AdminController@user");
});

Route::group(['prefix' => "listing"], function () {
    Route::post('create', "ListingController@create");
    Route::post('delete', "ListingController@delete");
    Route::post('approval', "ListingController@approval");
    Route::post('{id}/update', "ListingController@update");
    Route::get('{id}', "ListingController@getByID");
    Route::get('/', "ListingController@get");
});

Route::group(['prefix' => "page"], function () {
    Route::group(['prefix' => "admin"], function () {
        Route::get('dashboard', "AdminController@dashboard");
    });
    Route::get('home', "PageController@home");
});

Route::group(['prefix' => "reservation"], function () {
    Route::post('submit', "UserController@makeReservation");
    Route::get('/', "AdminController@reservation");
});

Route::group(['prefix' => "rajaongkir"], function () {
    Route::post('province', "RajaongkirController@province");
    Route::post('city/{provinceID}', "RajaongkirController@city");
    Route::post('district/{provinceID}/{cityID}', "RajaongkirController@district");
});