<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checker_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('summary');
            $table->text('error')->nullable();
            $table->unsignedInteger('update_count')->default(0);
            $table->timestamp('created_at')->nullable();

            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checker_results');
    }
};
