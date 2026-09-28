<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('onboarding_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 120);
            $table->string('phone', 30)->nullable();
            $table->string('business_name', 120);
            $table->string('business_type', 80)->nullable();
            $table->unsignedInteger('branch_count')->default(1);
            $table->string('status', 20)->default('New');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('onboarding_leads'); }
};
