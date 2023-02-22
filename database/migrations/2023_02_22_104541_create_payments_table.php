<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(config('simple-payment.user_model'))->nullable()->constrained()->restrictOnDelete();
            $table->uuidMorphs('payable');
            $table->string('status');
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->string('gateway_transaction_id');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
