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
        Schema::create('boxes_with_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned();
            $table->foreignId('box_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('box_id')
                ->references('id')
                ->on('boxes');
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
        Schema::dropIfExists('boxes_with_people');
    }
};
