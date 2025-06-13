<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('codeanalyzer_job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('codeanalyzer_jobs');
            $table->string('path');
            $table->string('blob_sha');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codeanalyzer_job_items');
    }
};
