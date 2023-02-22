<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(User::class)->nullable()->constrained()->restrictOnDelete();
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
