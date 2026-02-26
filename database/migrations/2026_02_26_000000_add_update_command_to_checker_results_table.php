<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checker_results', function (Blueprint $table) {
            $table->text('update_command')->nullable()->after('update_hint');
        });
    }

    public function down(): void
    {
        Schema::table('checker_results', function (Blueprint $table) {
            $table->dropColumn('update_command');
        });
    }
};
