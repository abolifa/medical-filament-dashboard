<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['medicine', 'equipment', 'service'])->default('medicine');
            $table->string('name');
            $table->string('image')->nullable();
            $table->boolean('expiry')->default(false);
            $table->date('expiry_date')->nullable();
            $table->text('usage')->nullable();
            $table->text('dosage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
