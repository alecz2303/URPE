<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('second_last_name')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('secondary_phone', 32)->nullable();
            $table->string('email')->nullable();
            $table->text('administrative_notes')->nullable();
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
        });

        Schema::create('guardian_patient', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->string('relationship', 100)->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamps();

            $table->unique(['patient_id', 'guardian_id']);
            $table->index(['patient_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardian_patient');
        Schema::dropIfExists('guardians');
    }
};
