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
         Schema::table('boxes', function (Blueprint $table){
                    $table->boolean('gift_sent')->default(false);
                    $table->boolean('gift_received')->default(false);
                    $table->boolean('draw_done')->default(false);
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boxes', function (Blueprint $table){
            $table->dropColumn('gift_sent');
            $table->dropColumn('gift_received');
            $table->dropColumn('draw_done');
        });
    }
};
