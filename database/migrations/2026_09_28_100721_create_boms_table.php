<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->unsignedInteger('output_quantity')->default(1);
            $table->string('status', 20)->default('Active');
            $table->string('notes', 160)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('boms'); }
};
