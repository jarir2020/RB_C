<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no', 30)->unique();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->date('returned_on');
            $table->unsignedInteger('quantity');
            $table->decimal('refund_amount', 14, 2);
            $table->string('reason', 120);
            $table->string('status', 20)->default('Completed');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('sales_returns'); }
};
