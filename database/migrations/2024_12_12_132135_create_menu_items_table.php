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
        Schema::disableForeignKeyConstraints();

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('qty');
            $table->boolean('availability')->nullable()->default(TRUE);
            $table->integer('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
