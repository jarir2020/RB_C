<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role', 80);
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('joined_on')->nullable();
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('employees'); }
};
