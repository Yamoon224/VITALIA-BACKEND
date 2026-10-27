<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_sessions', function (Blueprint $table): void {
            $table->string('motif')->nullable()->after('user_id');
            $table->string('protocol_code')->nullable()->after('motif');
            $table->json('answers')->nullable()->after('protocol_code');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_sessions', function (Blueprint $table): void {
            $table->dropColumn(['motif', 'protocol_code', 'answers']);
        });
    }
};
