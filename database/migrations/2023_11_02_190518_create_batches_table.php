<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockEntry;
use App\Models\StockExit;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->date('date');

            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(Warehouse::class)->constrained();
            $table->foreignIdFor(StockEntry::class)->nullable()->default(null)->constrained();
            $table->foreignIdFor(StockExit::class)->nullable()->default(null)->constrained();
            $table->float('quantity', 8, 2, true)->default(0);
            $table->float('unit_price', 12, 3, true)->default(0);
            $table->text('note')->nullable()->default(null);
            $table->boolean('confirmed')->nullable()->default(false);

            // $table->string('serial')->unique()
            //     ->nullable()
            //     ->default(null);
            // $table->string('lot')->unique()
            //     ->nullable()
            //     ->default(null);
            // $table->date('manufacture_date')->nullable()
            //     ->default(null);
            // $table->date('expiry_date')->nullable()
            //     ->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
