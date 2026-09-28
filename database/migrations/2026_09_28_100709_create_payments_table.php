<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no', 30)->unique();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('paid_on');
            $table->decimal('amount', 14, 2);
            $table->string('method', 40);
            $table->string('reference', 80)->nullable();
            $table->string('status', 20)->default('Completed');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('payments'); }
};
