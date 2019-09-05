<?php

/**
 * Route for news module
 */

Route::group(['namespace' => 'Avl\AdminBuilder\Controllers\Admin', 'middleware' => ['web', 'admin'], 'as' => 'adminbuilder::'], function () {
		Route::post('constructor/getData/{id}', 'ConstructorController@getData')->name('constructor.getData');
		Route::resource('constructor', 'ConstructorController');

		Route::post('sections/{id}/builder/getData/{table?}', 'BuilderController@getData')->name('sections.builder.getData');
		Route::post('sections/{id}/builder/getTables', 'BuilderController@getTables')->name('sections.builder.getTables');
		Route::resource('sections/{id}/builder', 'BuilderController', ['as' => 'sections'])->only([ 'index', 'update', 'destroy' ]);
});

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localizationRedirect']], function() {

	Route::group(['namespace' => 'Avl\AdminBuilder\Controllers'], function() {

		Route::post('builder/get-graph/{id}', 'CommonController@getGraphData');

		Route::group(['namespace' => 'Site'], function() {
			Route::get('builder/{alias}', 'BuilderController@index')->name('site.builder.index');
			Route::get('builder/{alias}/{id}', 'BuilderController@show')->name('site.builder.show');
		});
	});
});
