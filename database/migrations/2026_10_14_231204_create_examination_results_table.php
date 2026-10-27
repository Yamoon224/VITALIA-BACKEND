<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examination_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('examination_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('parameter');
            $table->decimal('value', 10, 3);
            $table->string('unit');
            $table->decimal('reference_low', 10, 3)->nullable();
            $table->decimal('reference_high', 10, 3)->nullable();
            $table->boolean('is_abnormal')->default(false);
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['user_id', 'parameter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examination_results');
    }
};
