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
        Schema::table('codeanalyzer_job_issues', function (Blueprint $table) {
            $table->string('git_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('codeanalyzer_job_issues', function (Blueprint $table) {
            $table->dropColumn('git_url');
        });
    }
};
