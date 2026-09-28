<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 30)->unique();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('sold_at');
            $table->decimal('total', 14, 2);
            $table->string('status', 20)->default('Pending');
            $table->string('channel', 40)->default('Retail');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sales'); }
};
