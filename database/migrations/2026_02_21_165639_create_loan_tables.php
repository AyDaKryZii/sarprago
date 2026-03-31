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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); 
            
            $table->dateTime('borrowed_at')->nullable();
            $table->dateTime('due_at'); 
            $table->dateTime('finished_at')->nullable();
            
            $table->enum('status', [
                'pending', 'approved', 'partially_approved', 'on_going', 'rejected', 'finished', 'cancelled'
            ])->default('pending');
            
            $table->text('reason')->nullable(); 
            $table->text('admin_note')->nullable(); 
            $table->timestamps();
        });

        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            
            $table->integer('qty_request'); 
            $table->integer('qty_approved')->default(0); 
            
            $table->unique(['loan_id', 'item_id']); 
            $table->timestamps();
        });

        Schema::create('loan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_item_id')->constrained('loan_items')->cascadeOnDelete();
            $table->foreignId('item_unit_id')->constrained('item_units')->cascadeOnDelete();
            
            $table->enum('condition_out', ['good', 'damaged'])->default('good');
            $table->enum('condition_in', ['good', 'damaged', 'broken', 'lost'])->nullable(); 
            
            $table->dateTime('returned_at')->nullable(); 
            
            $table->unique(['loan_item_id', 'item_unit_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_details');
        Schema::dropIfExists('loan_items');
        Schema::dropIfExists('loans');
    }
};