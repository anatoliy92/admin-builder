<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowspanColspanIntoBuilderTableData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('builder-table-data', function (Blueprint $table) {
            $table->integer('rowspan')->nullable();
            $table->integer('colspan')->nullable();
            $table->boolean('hide')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('builder-table-data', function (Blueprint $table) {
            $table->dropColumn('rowspan');
            $table->dropColumn('colspan');
            $table->dropColumn('hide');
        });
    }
}
