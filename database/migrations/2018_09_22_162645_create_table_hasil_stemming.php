<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHasilStemming extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_hasil_stemming', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->text('tweet');
            $table->string('sentimen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_hasil_stemming', function (Blueprint $table) {
            //
        });
    }
}
