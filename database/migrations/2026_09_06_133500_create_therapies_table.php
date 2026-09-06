<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapies', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedSmallInteger('required_therapists');
            $table->string('color', 7);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapies');
    }
};
