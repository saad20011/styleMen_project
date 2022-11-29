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
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('statut')->default(1);
            $table->foreignId('account_id')->constrained();
            $table->foreignId('type_size_id')->constrained();
            // $table->string('photo')->nullable();
            // $table->string('photo_dir')->nullable();
            $table->timestamps();
            // $table->foreignId('user_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sizes');
    }
};
