<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateBuilderTableDataTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create('builder-table-data', function (Blueprint $table) {
				$table->integer('table_id');
				$table->integer('row');
				$table->integer('col');

				$table->primary(['table_id', 'row', 'col']);

				$table->boolean('head')->default(false);

				$langs = Langs::all();
				foreach ($langs as $lang) { $table->string('value_' . $lang->key)->nullable(); }

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
			Schema::dropIfExists('builder-table-data');
		}
}
