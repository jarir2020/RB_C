<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('type', 30);
            $table->decimal('balance', 14, 2)->default(0);
            $table->string('trend', 20)->default('0%');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ledger_accounts'); }
};
