<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Jobstatus;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('codeanalyzer_job_status', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unique('id')->nullable();
            $table->primary('id');
            $table->string('name');
            $table->timestamps();
        });

        Jobstatus::firstOrCreate([
            'id' => 0,
            'name' => 'In wachtrij',
        ]);

        Jobstatus::firstOrCreate([
            'id' => 1,
            'name' => 'Verwerkt',
        ]);

        Jobstatus::firstOrCreate([
            'id' => 2,
            'name' => 'Fout',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codeanalyzer_job_status');
    }
};
