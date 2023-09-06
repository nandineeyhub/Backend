<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Module;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'Role',
            'School',
            'Users',
            'Enquiry',
        ];

        foreach ($modules as $module) {
            Module::insert([
                'name' => $module,
                'slug' => Str::slug($module),
            ]);
        }
    }
}
