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
        Schema::create('product_depots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_size_id')->constrained();
            $table->foreignId('depot_id')->constrained();
            $table->integer('quantity')->length(11)->default(0);
            $table->foreignId('account_id')->constrained();
            $table->string('status')->default(1);
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
        Schema::dropIfExists('product_depots');
    }
};
