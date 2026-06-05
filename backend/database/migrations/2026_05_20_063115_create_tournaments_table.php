<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('start_date');
            $table->string('fee')->default('FREE');
            $table->integer('max_teams')->default(16);
            $table->integer('registered_teams')->default(0);
            $table->string('status')->default('open');
            $table->string('prize_pool')->default('Trophies & Medals');
            $table->string('format')->default('Single Elimination');
            $table->string('organizer')->default('PlayConnect Official');
            $table->string('banner_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
};
