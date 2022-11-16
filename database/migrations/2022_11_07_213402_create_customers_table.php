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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('adresse_id')->constrained();
            $table->foreignId('phone_id')->constrained();
            $table->string('note')->nullable();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('statut')->length(11)->default(1);
            $table->string('adresse')->nullable();
            $table->string('phons')->nullable();
            $table->string('whatsapphone')->nullable();
            $table->string('facebook')->nullable();
            $table->string('comment')->nullable();
            $table->foreignId('city_id')->constrained();
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
        Schema::dropIfExists('customers');
    }
};
