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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->string('capture_id');
            $table->string('product_name');
            $table->integer('quantity');
            $table->double('capture_amount');
            $table->string('currency');
            $table->string('payer_name');
            $table->string('payer_email');
            $table->string('payment_status');
            $table->double('platform_fees');
            $table->string('payment_method');
            $table->double('seller_receivable_final_amount');
            $table->string('refund_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
