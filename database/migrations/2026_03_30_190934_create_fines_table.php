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
    Schema::create('fines', function (Blueprint $table) {
        $table->id();
        // Foreign Key ke Loan & User
        $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
        // Detail Denda
        $table->decimal('amount', 12, 2); // Contoh: 50000.00
        $table->string('reason'); // 'Terlambat 3 hari', 'Barang lecet', dsb.
        
        // Status Pembayaran
        $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
        $table->timestamp('paid_at')->nullable(); // Kapan dibayar
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
