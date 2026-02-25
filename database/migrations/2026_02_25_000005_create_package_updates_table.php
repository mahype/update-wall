<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checker_result_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('current_version', 100);
            $table->string('new_version', 100);
            $table->string('type', 50);
            $table->string('priority', 20)->default('normal');
            $table->string('source')->nullable();
            $table->string('phasing', 100)->nullable();

            $table->index('checker_result_id');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_updates');
    }
};
