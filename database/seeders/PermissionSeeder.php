<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
                'Add',
                'Edit',
                'View',
                'Update',
                'Delete'
        ];

        foreach ($permissions as $permission) {
            Permission::insert([
                'name' => $permission,
                'slug' => Str::slug($permission),
            ]);
        }
    }
}
