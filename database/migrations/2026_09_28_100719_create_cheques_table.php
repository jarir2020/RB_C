<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cheque_no', 50);
            $table->date('cheque_date');
            $table->decimal('amount', 14, 2);
            $table->string('direction', 20)->default('In');
            $table->string('status', 20)->default('Pending');
            $table->string('notes', 160)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cheques'); }
};
