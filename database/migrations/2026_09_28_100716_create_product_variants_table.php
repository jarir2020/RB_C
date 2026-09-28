<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('sku', 40)->unique();
            $table->string('barcode', 80)->nullable()->unique();
            $table->json('attributes')->nullable();
            $table->decimal('price', 14, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(5);
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('product_variants'); }
};
