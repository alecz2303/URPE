<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_session_log_amendments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinical_session_log_id')->constrained()->restrictOnDelete();
            $table->foreignId('authored_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 500);
            $table->text('content');
            $table->timestamps();

            $table->index(['clinical_session_log_id', 'created_at'], 'session_log_amendments_chronology');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_session_log_amendments');
    }
};
