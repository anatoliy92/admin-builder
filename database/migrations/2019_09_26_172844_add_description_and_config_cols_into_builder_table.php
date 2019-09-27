<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class AddDescriptionAndConfigColsIntoBuilderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('builder-table', function (Blueprint $table) {
            $langs = Langs::all();
            foreach ($langs as $lang) { $table->mediumText('before_' . $lang->key)->nullable(); }
            foreach ($langs as $lang) { $table->mediumText('after_' . $lang->key)->nullable(); }

            $table->json('config')->nullable();
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
            $langs = Langs::all();
            foreach ($langs as $lang) { $table->dropColumn('before_' . $lang->key); }
            foreach ($langs as $lang) { $table->dropColumn('after_' . $lang->key); }

            $table->dropColumn('config');
        });
    }
}
