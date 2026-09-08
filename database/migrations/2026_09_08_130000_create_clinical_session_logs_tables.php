<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_session_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('therapy_id')->constrained()->restrictOnDelete();
            $table->foreignId('authored_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft');
            $table->text('treatment_activities');
            $table->text('patient_response')->nullable();
            $table->text('observations_incidents')->nullable();
            $table->text('home_recommendations')->nullable();
            $table->text('next_session_objectives')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'created_at']);
        });

        Schema::create('clinical_session_log_therapist', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinical_session_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['clinical_session_log_id', 'therapist_id'], 'session_log_therapist_unique');
        });

        Schema::create('appointment_therapist_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->restrictOnDelete();
            $table->foreignId('removed_therapist_id')->nullable()->constrained('therapists')->nullOnDelete();
            $table->foreignId('added_therapist_id')->nullable()->constrained('therapists')->nullOnDelete();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 500)->nullable();
            $table->timestamps();

            $table->index(['appointment_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_therapist_changes');
        Schema::dropIfExists('clinical_session_log_therapist');
        Schema::dropIfExists('clinical_session_logs');
    }
};
