<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            'email' => 'admin@test.com'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email'=>'admin@test.com',
            'password' => bcrypt('admin123')
        ]);
    }
}
