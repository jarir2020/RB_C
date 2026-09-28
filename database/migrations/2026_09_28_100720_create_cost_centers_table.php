<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 30)->unique();
            $table->decimal('budget', 14, 2)->default(0);
            $table->decimal('spent', 14, 2)->default(0);
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cost_centers'); }
};
