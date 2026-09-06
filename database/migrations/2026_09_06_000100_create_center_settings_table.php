<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('center_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->default('URPE');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('timezone')->default('America/Mexico_City');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('center_settings');
    }
};
