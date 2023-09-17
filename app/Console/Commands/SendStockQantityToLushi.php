<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Console\Command;

class SendStockQantityToLushi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:from-store-to-lushi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line("Import starting...");

        $lushi = Shop::where('name', Shop::LUBUMBASHI)->first();
        $count = 0;

        foreach (ShopProduct::where('shop_id', $lushi->id)->get() as $shopProd) {
            $product = Product::where('id', $shopProd->product_id)->first();
            $shopProd->quantity = $product->quantity;
            $shopProd->save();

            $product->quantity = 0;
            $product->save();
            $count += 1;
        }

        $this->line("Quantities updated for: $count items");
    }
}
