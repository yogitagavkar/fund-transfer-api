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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 50)
                ->unique();
            $table->string('account_holder_name',255);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('balance',15,2)->default(0.00);
            $table->string('currency',3)->default('USD');
            $table->enum('status',
                [
                    'active',
                    'inactive',
                    'suspended'
                ]
            )->default('active');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
