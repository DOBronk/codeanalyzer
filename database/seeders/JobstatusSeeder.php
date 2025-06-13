<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('codeanalyzer_job_status')->insert([
            'id' => 0,
            'name' => 'In wachtrij',
        ]);
        DB::table('codeanalyzer_job_status')->insert([
            'id' => 1,
            'name' => 'Verwerkt',
        ]);
        DB::table('codeanalyzer_job_status')->insert([
            'id' => 2,
            'name' => 'Fout',
        ]);
        DB::table('codeanalyzer_job_status')->insert([
            'id' => 3,
            'name' => 'Issue aangemaakt',
        ]);
    }
}
