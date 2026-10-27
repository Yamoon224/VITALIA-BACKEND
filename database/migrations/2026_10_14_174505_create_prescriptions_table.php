<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('practitioner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('draft');
            $table->json('items');
            $table->json('safety_alerts')->nullable();
            $table->text('refusal_reason')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_hash')->nullable();
            $table->timestamp('transmitted_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
