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
            $table->foreignId('invited_user_id')->nullable();
            $table->foreign('invited_user_id')->references('id')->on('invited_users')->cascadeOnDelete();
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
            $table->dropColumn('invited_user_id');
        });
    }
};
