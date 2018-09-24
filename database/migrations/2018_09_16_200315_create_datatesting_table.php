<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatatestingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_datatesting', function (Blueprint $table) {
            $table->string('user_id')->primary();
            $table->string('username');
            $table->string('tweet_id');
            $table->text('tweet');
            $table->text('sentiment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datatesting');
    }
}
