<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->longText('medical_history')->nullable();
            $table->longText('prenatal_perinatal_history')->nullable();
            $table->longText('developmental_history')->nullable();
            $table->longText('family_history')->nullable();
            $table->longText('diagnoses')->nullable();
            $table->longText('therapeutic_objectives')->nullable();
            $table->longText('general_observations')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_records');
    }
};
