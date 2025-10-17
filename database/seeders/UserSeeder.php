<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Tesko',
            'email' => 'theonicolas.foucher@gmail.com',
            'password' => Hash::make('Magouille.824699'),
        ]);

        User::create([
            'name' => 'Blitzen',
            'email' => 'alexrousse53@gmail.com',
            'password' => Hash::make('Blitzenn'),
        ]);
    }
}
