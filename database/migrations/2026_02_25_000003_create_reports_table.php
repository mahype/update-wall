<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->timestamp('reported_at');
            $table->unsignedInteger('total_updates')->default(0);
            $table->boolean('has_security')->default(false);
            $table->json('raw_payload')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['machine_id', 'reported_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
