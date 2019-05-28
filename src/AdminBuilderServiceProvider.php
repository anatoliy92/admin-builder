<?php namespace Avl\AdminBuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Config;

class AdminBuilderServiceProvider extends ServiceProvider
{

		/**
		 * Bootstrap the application services.
		 *
		 * @return void
		 */
		public function boot()
		{
				// Публикуем файл конфигурации
				$this->publishes([
						__DIR__ . '/../config/adminbuilder.php' => config_path('adminbuilder.php'),
				]);

				$this->publishes([
						__DIR__ . '/../public' => public_path('vendor/adminbuilder'),
				], 'public');

				$this->loadRoutesFrom(__DIR__ . '/routes.php');

				$this->loadViewsFrom(__DIR__ . '/../resources/views', 'adminbuilder');
		}

		/**
		 * Register the application services.
		 *
		 * @return void
		 */
		public function register()
		{
				// Добавляем в глобальные настройки системы новый тип раздела
				Config::set('avl.sections.builder', 'Табличный раздел');

				// объединение настроек с опубликованной версией
				$this->mergeConfigFrom(__DIR__ . '/../config/adminbuilder.php', 'adminbuilder');

				// migrations
				$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

				$this->loadHelpers();
		}

		/**
		 * Load helpers.
		 */
		protected function loadHelpers()
		{
				foreach (glob(__DIR__ . '/Helpers/*.php') as $filename) {
						require_once $filename;
				}
		}

}
