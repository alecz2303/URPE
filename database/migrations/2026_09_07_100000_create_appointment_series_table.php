<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_series', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('therapy_id')->constrained()->restrictOnDelete();
            $table->time('starts_at_time');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->json('weekdays');
            $table->timestamps();
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->foreignId('appointment_series_id')
                ->nullable()
                ->after('therapy_id')
                ->constrained('appointment_series')
                ->nullOnDelete();
            $table->unsignedInteger('series_occurrence')->nullable()->after('appointment_series_id');
            $table->unique(['appointment_series_id', 'series_occurrence']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropUnique(['appointment_series_id', 'series_occurrence']);
            $table->dropConstrainedForeignId('appointment_series_id');
            $table->dropColumn('series_occurrence');
        });

        Schema::dropIfExists('appointment_series');
    }
};
