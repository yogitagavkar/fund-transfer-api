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
        Schema::create('transactions', function (Blueprint $table) {
             $table->id();
             $table->unsignedBigInteger('from_account_id');
             $table->unsignedBigInteger('to_account_id');
             $table->decimal('amount',15,2);
             $table->string(
                'description',
                500
             )->nullable();

            $table->string('reference_id',100
            )->nullable()->unique();

            $table->enum('status',['pending','completed','failed','reversed']
                )->default('pending');

            $table->longText(
                'error_message'
            )->nullable();

            $table->timestamps();

            $table->foreign('from_account_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('to_account_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index([
                'from_account_id',
                'created_at'
            ]);

            $table->index([
                'to_account_id',
                'created_at'
            ]);

            $table->index('reference_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
