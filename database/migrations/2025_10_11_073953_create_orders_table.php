<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger( 'user_id');
            $table->unsignedBigInteger( 'address_id');
            $table->unsignedBigInteger( 'coupon_id')->nullable();
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->tinyInteger("type");
            $table->double("total_price");
            $table->double("delivery_price")->default(0);
            $table->tinyInteger("payment_method");
            $table->tinyInteger("status")->default(0);
            $table->tinyInteger("rating")->nullable();
            $table->string("rating_comment")->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
