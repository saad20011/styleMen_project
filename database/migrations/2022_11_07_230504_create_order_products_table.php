<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('products_size_id')->constrained();
            $table->foreignId('offer_id')->constrained();
            $table->float('price',8,2);
            $table->integer('quantity')->length(11)->default(1);
            $table->foreignId('user_id')->constrained();
            $table->string('status')->default(1);
            $table->foreignId('account_user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_products');
    }
};
