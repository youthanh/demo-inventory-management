<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('address')->nullable()
                ->default(null);
            $table->timestamps();
        });

        // Bảng lô hàng
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('serial')->unique()
                ->nullable()
                ->default(null);
            $table->string('lot')->unique()
                ->nullable()
                ->default(null);
            $table->float('quantity')->default(0);
            $table->date('manufacture_date')->nullable()
                ->default(null);
            $table->date('expiry_date')->nullable()
                ->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
