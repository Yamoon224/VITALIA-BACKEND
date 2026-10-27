<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('specialty');
            $table->string('license_number');
            $table->timestamp('verified_at')->nullable();
            $table->text('bio')->nullable();
            $table->json('languages')->nullable();
            $table->unsignedInteger('consultation_fee_cents')->nullable();
            $table->timestamps();

            $table->index('specialty');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_profiles');
    }
};
