<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'nama' => 'Owner BRILink',
            'nomor_hp' => '08123456789',
            // 'username' => 'admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'status' => 'aktif',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::create([
            'nama' => 'Kasir BRILink',
            'nomor_hp' => '08123456789',
            // 'username' => 'admin',
            'email' => 'kasir@gmail.com',
            'role' => 'kasir',
            'status' => 'aktif',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
