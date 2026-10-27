<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table): void {
            $table->id();
            $table->string('dci')->unique();
            $table->string('form');
            $table->string('standard_dosage');
            $table->unsignedSmallInteger('max_duration_days');
            $table->boolean('is_restricted')->default(false);
            $table->json('allergen_tags')->nullable();
            $table->json('contraindications')->nullable();
            $table->json('interacts_with')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
