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
        Schema::table('codeanalyzer_job_items', function (Blueprint $table) {
            $table->renameColumn('blob_sha', 'sha')->change();
            $table->integer('status_id')->default(0)->change();
            $table->text('results')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('codeanalyzer_job_items', function (Blueprint $table) {
            //
        });
    }
};
