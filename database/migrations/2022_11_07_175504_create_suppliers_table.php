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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('phone_id')->constrained();
            $table->foreignId('adresse_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->string('photo')->nullable();
            $table->string('photo_dir')->nullable();
            $table->integer('statut')->default(1);
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
        Schema::dropIfExists('suppliers');
    }
};
