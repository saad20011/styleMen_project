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
        Schema::create('delivery_mens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('carrier_id')->constrained();
            $table->string('photo')->nullable();
            $table->string('photo_dir')->nullable();
            $table->integer('statut')->length(11)->default(1);
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
        Schema::dropIfExists('delivery_mens');
    }
};
