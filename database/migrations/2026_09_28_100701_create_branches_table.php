<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('city', 80);
            $table->decimal('sales_target', 14, 2)->default(0);
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('branches'); }
};
