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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('code_prefix');
            $table->string('image_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('unit_code')->unique();
            $table->integer('sort_order');
            $table->enum('condition', ['good', 'damaged', 'broken'])->default('good');
            $table->enum('status', ['available', 'reserved', 'unavailable', 'borrowed', 'lost', 'maintenance'])->default('available');
            $table->string('image_path')->nullable();
            $table->json('attributes')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['item_id', 'unit_code']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_units');
        Schema::dropIfExists('items');
    }
};
