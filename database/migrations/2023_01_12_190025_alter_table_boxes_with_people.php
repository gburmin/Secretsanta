<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boxes_with_people', function (Blueprint $table) {
            $table->foreignId('secret_santa_to_id')->nullable();
            $table->foreign('secret_santa_to_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boxes_with_people', function (Blueprint $table) {
            $table->dropColumn('secret_santa_to_id');
        });
    }
};
