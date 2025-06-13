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
        Schema::create('codeanalyzer_job_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('codeanalyzer_jobs');
            $table->foreignId('jobitem_id')->constrained('codeanalyzer_job_items');
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->binary('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codeanalyzer_job_issues');
    }
};
