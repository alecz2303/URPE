<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('therapy_id')->constrained()->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('duration_minutes');
            $table->string('status', 32)->default('scheduled');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['starts_at', 'ends_at']);
            $table->index(['status', 'starts_at']);
        });

        Schema::create('appointment_therapist', function (Blueprint $table): void {
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->primary(['appointment_id', 'therapist_id']);
            $table->index(['therapist_id', 'appointment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_therapist');
        Schema::dropIfExists('appointments');
    }
};
