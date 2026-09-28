<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_no', 30)->unique();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->date('transferred_on');
            $table->unsignedInteger('quantity');
            $table->string('status', 20)->default('In transit');
            $table->string('note', 160)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('stock_transfers'); }
};
