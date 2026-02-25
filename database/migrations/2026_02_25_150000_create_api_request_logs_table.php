<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('ip', 45);
            $table->string('status', 30);
            $table->foreignId('token_id')->nullable()->constrained('api_tokens')->nullOnDelete();
            $table->string('hostname')->nullable();
            $table->string('detail', 100)->nullable();

            $table->index('created_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};
