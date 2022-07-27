<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->integer('trip_count')->default(0);
            $table->decimal('trip_miles', 8, 1)->default(0);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->decimal('total', 8, 1);
            $table->decimal('miles', 8, 1);
            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::drop('cars');
        Schema::drop('trips');
        Schema::enableForeignKeyConstraints();
    }
};
