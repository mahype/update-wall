<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('checker_results', function (Blueprint $table) {
            $table->text('update_hint')->nullable()->after('error');
        });
    }

    public function down(): void
    {
        Schema::table('checker_results', function (Blueprint $table) {
            $table->dropColumn('update_hint');
        });
    }
};
