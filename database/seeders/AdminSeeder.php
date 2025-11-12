<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'BienvenuAdmin',
                'password' => Hash::make('123456'), // Hash du mot de passe
            ],
            [
                'name' => 'MecodeAdmin',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'DianeAdmin',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'MariolAdmin',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'AmiirAdmin',
                'password' => Hash::make('123456'),
            ],
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}