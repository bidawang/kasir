<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Owner Agen',
                'nomor_hp' => '08123456789',
                'username' => 'admin',
                'role' => 'admin',
                'status' => 'aktif',
                'password' => 'password',
            ]
        );

        User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'nama' => 'Kasir Agen',
                'nomor_hp' => '08123456789',
                'username' => 'kasir',
                'role' => 'kasir',
                'status' => 'aktif',
                'password' => 'password',
            ]
        );

        User::firstOrCreate(
            ['email' => 'developer@gmail.com'],
            [
                'nama' => 'Master Developer',
                'nomor_hp' => '081122334455',
                'username' => 'developer',
                'role' => 'developer',
                'status' => 'aktif',
                'password' => 'password',
            ]
        );
    }
}
