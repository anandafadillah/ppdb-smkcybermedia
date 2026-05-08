<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->upsert(
            [
                [
                    'name'       => 'Administrator',
                    'nisn'       => null,
                    'email'      => 'admin@smkcybermedia.sch.id',
                    'password'   => Hash::make('Admin@1234'),
                    'role'       => 'admin',
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['email'],
            ['name', 'password', 'role', 'is_active', 'updated_at']
        );
    }
}
