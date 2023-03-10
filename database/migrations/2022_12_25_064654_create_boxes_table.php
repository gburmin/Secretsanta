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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Название коробки');
            $table->text('description')->nullable();
            $table->string('cover')->default('');
            $table->boolean('email')->default(false);
            $table->boolean('isPrivate')->default(false);
            $table->integer('cost')->nullable();
            $table->integer('max_people_in_box')->nullable();
            $table->timestamp('draw_starts_at')->nullable();
            $table->foreignId('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
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
        Schema::dropIfExists('boxes');
    }
};
