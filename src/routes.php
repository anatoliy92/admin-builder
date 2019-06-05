<?php

/**
 * Route for news module
 */

Route::group(['namespace' => 'Avl\AdminBuilder\Controllers\Admin', 'middleware' => ['web', 'admin'], 'as' => 'adminbuilder::'], function () {
		Route::post('constructor/getData/{id}', 'ConstructorController@getData')->name('constructor.getData');
		Route::resource('constructor', 'ConstructorController');
		Route::post('sections/{id}/builder/getData', 'BuilderController@getData')->name('sections.builder.getData');
		Route::resource('sections/{id}/builder', 'BuilderController', ['as' => 'sections'])->only([ 'index', 'update' ]);
});

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localizationRedirect']], function() {
	Route::group(['namespace' => 'Avl\AdminBuilder\Controllers\Site'], function() {
		// Route::get('builder/{alias}', 'BuilderController@index');
	});
});
