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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code',100)->nullable(); //
            $table->foreignId('customer_id')->constrained(); // aprés la creation customer
            $table->foreignId('accounts_carrier_id')->constrained(); //
            $table->foreignId('account_user_id')->constrained();
            $table->string('adresse')->nullable();
            $table->foreignId('accounts_city_id')->constrained();
            $table->string('shipping_code')->nullable();
            $table->double('discount',8,2)->default(0);
            $table->double('carrier_price',8,2)->default(0);
            $table->double('total',8,2)->default(0);
            $table->double('real_price',8,2)->nullable();
            $table->foreignId('delivery_men_id')->constrained();
            $table->double('real_carrier_price',8,2)->default(0);
            $table->foreignId('shipping_id')->constrained();
            $table->foreignId('invoice_id')->constrained();
            $table->string('comment')->nullable();
            $table->foreignId('status_id')->constrained();
            $table->foreignId('payment_method_id')->constrained();
            $table->foreignId('brands_source_id')->constrained();
            $table->integer('sms')->default(0);
            $table->integer('return')->nullable();
            $table->integer('affected')->nullable();
            $table->foreignId('payment_commission_id')->constrained();
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
        // Schema::dropIfExists('orders');
        Schema::table( "orders", function(Blueprint $table )
        {
            $table->dropForeign('payment_id');
            // $table->dropColumn('parent_id');
        } );
    }
};
