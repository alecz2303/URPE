<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('center_operating_hours', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('day_of_week');
            $table->boolean('is_enabled')->default(true);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['day_of_week', 'is_enabled']);
            $table->unique(['day_of_week', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('center_operating_hours');
    }
};
