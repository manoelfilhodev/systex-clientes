<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('method')->nullable(); // pix, boleto, cartão
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
