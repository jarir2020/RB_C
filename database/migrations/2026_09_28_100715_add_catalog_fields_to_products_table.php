<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand', 80)->nullable()->after('category');
            $table->string('unit', 30)->default('piece')->after('brand');
            $table->string('barcode', 80)->nullable()->unique()->after('sku');
            $table->decimal('cost_price', 14, 2)->default(0)->after('price');
            $table->decimal('wholesale_price', 14, 2)->default(0)->after('cost_price');
            $table->string('price_label', 30)->nullable()->after('wholesale_price');
            $table->text('description')->nullable()->after('price_label');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
            $table->dropColumn(['brand', 'unit', 'barcode', 'cost_price', 'wholesale_price', 'price_label', 'description']);
        });
    }
};
