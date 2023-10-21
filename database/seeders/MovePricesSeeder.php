<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovePricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Product::get() as $stockProduct) {
            if ($stockProduct->sell_price > 0) {
                $lushi = Shop::where('name', Shop::LUBUMBASHI)->first();
                $lushiProd = ShopProduct::where('shop_id', $lushi->id)
                    ->where('product_id', $stockProduct->id)
                    ->first();

                if ($lushiProd) {
                    \Log::info('********************************');
                    \Log::info($lushiProd->id);
                    \Log::info($lushiProd->sell_price);

                    $lushiProd->sell_price = $stockProduct->sell_price;
                    $lushiProd->save();

                    \Log::info($lushiProd->sell_price);
                }
            }
        }
    }
}
