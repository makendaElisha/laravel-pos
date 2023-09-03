<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LushiInitStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('shop_products')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $products = [
            [
                'name' => 'AMORTI ARR TVS100',
                'quantity' => 13,
                'sell_price' =>  45000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AMORTI ARR DT',
                'quantity' => 15,
                'sell_price' => 37000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AMORTI BX (PAIRE',
                'quantity' =>  1,
                'sell_price' => 37000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AMORTI TVS 5',
                'quantity' => 40,
                'sell_price' => 5000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AMPOULE PHAR SIMBA',
                'quantity' => 88,
                'sell_price' =>  5500,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AMPOULE QUIGNOTANT 41',
                'quantity' => 2,
                'sell_price' => 500,
                'items_in_box' => 1,
            ],
            [
                'name' => 'ARBRE ACAM BX',
                'quantity' => 89,
                'sell_price' =>  9000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'ARBRE ACAM TVS100',
                'quantity' => 11,
                'sell_price' => 4000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE ARR BX',
                'quantity' => 11,
                'sell_price' => 3000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE ARR FOURCHE',
                'quantity' => 20,
                'sell_price' => 2000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE ARR TVS100',
                'quantity' => 29,
                'sell_price' =>  25000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE AVANT BX',
                'quantity' => 21,
                'sell_price' => 7000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE BOITE BX',
                'quantity' => 25,
                'sell_price' =>  7000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE BOITE DT',
                'quantity' => 3,
                'sell_price' => 10000,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE CENTRAL BAJAJA',
                'quantity' => 0,
                'sell_price' => 2500,
                'items_in_box' => 1,
            ],
            [
                'name' => 'AXE CENTRAL BX',
                'quantity' => 11,
                'sell_price' => 42000,
                'items_in_box' => 1,
            ],
        ];

        foreach ($products as $key => $product) {
            $prod = Product::create(array_merge(['code' => $key + 1], $product));

            foreach (Shop::get() as $key => $shop) {
                ShopProduct::create([
                    'shop_id' => $shop->id,
                    'product_id' => $prod->id,
                    'quantity' => 2,
                ]);
            }
        }
    }
}
