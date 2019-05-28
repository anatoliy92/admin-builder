<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateBuilderTableTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create('builder-table', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('section_id')->nullable();
				$table->boolean('is_default')->default(false);

				$table->boolean('good')->default(false);
				$table->string('title')->nullable();
				// $langs = Langs::all();
				// foreach ($langs as $lang) { $table->boolean('good_' . $lang->key)->default(false); }
				// foreach ($langs as $lang) { $table->string('title_' . $lang->key)->nullable(); }

				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
				Schema::dropIfExists('builder-table');
		}
}
