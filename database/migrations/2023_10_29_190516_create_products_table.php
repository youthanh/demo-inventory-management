<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ProductCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // $table->foreignIdFor(ProductCategory::class)->nullable()->default(null)->constrained();

            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->string('order')->nullable()->default(null);
            $table->string('supplier')->nullable()->default(null);
            $table->float('unit_price', 12, 3, true)->default(0);
            
            // $table->decimal('cost_price', 12, 2)->default(0);
            // $table->decimal('selling_price', 12, 2)->default(0);
            // $table->integer('expiry_period')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
