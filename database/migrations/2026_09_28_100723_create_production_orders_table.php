<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('order_no', 40)->unique();
            $table->date('planned_on');
            $table->unsignedInteger('output_quantity');
            $table->string('status', 20)->default('Planned');
            $table->string('notes', 160)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('production_orders'); }
};
