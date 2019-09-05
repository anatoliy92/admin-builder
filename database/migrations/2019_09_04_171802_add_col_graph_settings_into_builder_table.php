<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColGraphSettingsIntoBuilderTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::table('builder-table', function (Blueprint $table) {
				$table->json('graph')->nullable();
			});
		}

		 /**
			* Reverse the migrations.
			*
			* @return void
			*/
		public function down()
		{
			Schema::table('builder-table', function (Blueprint $table) {
				$table->dropColumn('graph');
			});
		}
}
