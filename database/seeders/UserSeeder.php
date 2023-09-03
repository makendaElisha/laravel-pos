<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email'=>'admin@tadam.com',
            'is_admin' => true,
            'password' => bcrypt('admin123')
        ]);

        // Lubumbashi
        User::create([
            'first_name' => 'Ville',
            'last_name' => '',
            'email'=>'ville@tadam.com',
            'shop_name' => Shop::VILLE,
            'password' => bcrypt('ville123')
        ]);

        // Kolwezi
        User::create([
            'first_name' => 'Kolwezi',
            'last_name' => '',
            'email'=>'kolwezi@tadam.com',
            'shop_name' => Shop::KOLWEZI,
            'password' => bcrypt('kolwezi123')
        ]);

        // Kilwa
        User::create([
            'first_name' => 'Kilwa',
            'last_name' => '',
            'email'=>'kilwa@tadam.com',
            'shop_name' => Shop::KILWA,
            'password' => bcrypt('kilwa123')
        ]);
    }
}
