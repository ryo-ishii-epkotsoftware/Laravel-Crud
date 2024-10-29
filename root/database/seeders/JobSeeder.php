<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->isLocal()) {
            Job::factory()
            ->count(100)
            ->sequence(function ($sequence) {
                return [
                    'name' => sprintf('JOB_%04d', $sequence->index + 1),
                    'deleted_at' => null,
                    'created_at' => '2022-12-30 11:22:33',
                    'updated_at' => '2022-12-31 23:58:59',
                ];
            })
            ->create();
        }
    }
}
