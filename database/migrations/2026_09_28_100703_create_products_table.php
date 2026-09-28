<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku', 40)->unique();
            $table->string('category', 80);
            $table->decimal('price', 14, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(10);
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};
