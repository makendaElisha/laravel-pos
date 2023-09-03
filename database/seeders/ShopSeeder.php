<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('shops')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ville
        Shop::create([
            'name' => Shop::VILLE,
            'display' => '',
            'address' => '',
            'slug' => '',
        ]);

        // Kolwezi
        Shop::create([
            'name' => Shop::KOLWEZI,
            'display' => '',
            'address' => '',
            'slug' => '',
        ]);

        // Kilwa
        Shop::create([
            'name' => Shop::KILWA,
            'display' => '',
            'address' => '',
            'slug' => '',
        ]);
    }
}
