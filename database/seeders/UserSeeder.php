<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create the user Giancarlo Di Massa with email giancarlo@digitall.it and password e2p0s0o1n, no faker factory
        User::create([
            'name' => 'Giancarlo Di Massa',
            'email' => 'giancarlo@digitall.it',
            'password' => Hash::make('e2p0s0o1n'),
        ]);

    }
}
