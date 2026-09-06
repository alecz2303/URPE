<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_files', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('disk', 50)->default('clinical');
            $table->string('path', 500);
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->char('sha256', 64)->index();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_type')->nullable();
            $table->string('subject_id', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['uploaded_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_files');
    }
};
