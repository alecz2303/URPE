<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->string('type', 50)->index();
            $table->string('status', 30)->default('draft')->index();
            $table->date('examination_date');
            $table->foreignId('authored_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
            $table->string('instrument_version', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['patient_id', 'type', 'examination_date']);
        });

        Schema::create('hine_assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinical_assessment_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('gestational_age', 100)->nullable();
            $table->string('chronological_age', 100)->nullable();
            $table->string('corrected_age', 100)->nullable();
            $table->string('head_circumference', 100)->nullable();
            $table->decimal('cranial_nerves_score', 4, 1)->nullable();
            $table->decimal('posture_score', 4, 1)->nullable();
            $table->decimal('movements_score', 4, 1)->nullable();
            $table->decimal('tone_score', 4, 1)->nullable();
            $table->decimal('reflexes_reactions_score', 4, 1)->nullable();
            $table->decimal('global_score', 4, 1)->nullable();
            $table->unsignedSmallInteger('asymmetry_count')->default(0);
            $table->text('general_comments')->nullable();
            $table->timestamps();
        });

        Schema::create('hine_responses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hine_assessment_id')->constrained()->cascadeOnDelete();
            $table->string('section_key', 60);
            $table->string('item_key', 100);
            $table->decimal('score', 2, 1)->nullable();
            $table->boolean('asymmetry')->nullable();
            $table->json('response_data')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->unique(['hine_assessment_id', 'item_key']);
            $table->index(['hine_assessment_id', 'section_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hine_responses');
        Schema::dropIfExists('hine_assessments');
        Schema::dropIfExists('clinical_assessments');
    }
};
