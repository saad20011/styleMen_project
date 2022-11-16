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
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('phone_id')->constrained();
            $table->foreignId('adresse_id')->constrained();
            $table->string('email')->nullable();
            $table->string('trackinglink')->nullable();
            $table->integer('autocode')->length(11)->default(1);
            $table->string('photo')->nullable();
            $table->string('photo_dir')->nullable();
            $table->string('comment')->nullable();
            $table->integer('statut')->length(11)->nullable();
            $table->foreignId('user_id')->constrained();

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
        Schema::dropIfExists('carriers');
    }
};
