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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger( 'category_id');
            $table->string('name')->unique();
            $table->string('name_ar')->unique();
            $table->string('desc')->unique();
            $table->string('desc_ar')->unique();
            $table->integer('price');
            $table->integer('count');
            $table->integer('discount');
            $table->boolean('is_active');
            $table->string('image')->nullable();
             $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
