<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class AddAndChangeColTitleInBuilderTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('builder-table', function (Blueprint $table) {
				// Сначала удаляем старую колонку
				$table->dropColumn('title');

				$langs = Langs::all();
				// создаем новую по языкам
				foreach ($langs as $lang) { $table->string('title_' . $lang->key, 500)->nullable(); }

				$table->dateTime('published_at')->default(now());
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
				$table->string('title')->nullable();

				// удаляем те что были по языкам
				$langs = Langs::all();
				foreach ($langs as $lang) { $table->dropColumn('title_' . $lang->key); }
				$table->dropColumn('published_at');
		});
	}
}
