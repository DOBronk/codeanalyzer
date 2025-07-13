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
        Schema::table('codeanalyzer_jobs', function (Blueprint $table) {
            $table->renameColumn('repo', 'repository')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('codeanalyzer_jobs', function (Blueprint $table) {
            $table->renameColumn('repository', 'repo');
        });
    }
};
