<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('hostname')->unique();
            $table->string('display_name')->nullable();
            $table->foreignId('api_token_id')->nullable()->constrained('api_tokens')->nullOnDelete();
            $table->timestamp('last_report_at')->nullable();
            $table->unsignedInteger('total_updates')->default(0);
            $table->boolean('has_security')->default(false);
            $table->string('status', 20)->default('ok');
            $table->timestamps();

            $table->index('status');
            $table->index('last_report_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
