<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth()->user();
        $lushi = Shop::where('name', Shop::LUBUMBASHI)->first();
        $kolwezi = Shop::where('name', Shop::KOLWEZI)->first();
        $kilwa = Shop::where('name', Shop::KILWA)->first();

        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();

        $dailySells = Order::sum('total');

        $allDailySales = [
            Shop::LUBUMBASHI => $lushi ? Order::where('shop_id', $lushi->id)->sum('total') : '',
            Shop::KILWA => $kolwezi ? Order::where('shop_id', $kilwa->id)->sum('total') : '',
            Shop::KOLWEZI => $kilwa ? Order::where('shop_id', $kolwezi->id)->sum('total') : '',
        ];

        $lowStockProducts = Product::whereColumn('quantity', '<', 'min_quantity')->get();

        $allShopSales = 0;
        if (!$user->is_admin) {
            $currShop = Shop::where('name', $user->shop_name)->first();
            $dailySells = Order::where('shop_id', $currShop->id)->sum('total');

            $ids = [];
            $shpProds = ShopProduct::with('product')->where('shop_id', $currShop->id)->get();
            foreach ($shpProds as $prodItem) {
                if ($prodItem->quantity < $prodItem->product->min_quantity) {
                    $ids[] = $prodItem->product_id;
                }
            }

            $lowStockProducts = Product::whereIn('id', $ids)->get();
            foreach ($lowStockProducts as $prod) {
                $prod->shop_quantity = $shpProds->where('product_id', $prod->id)->first()->quantity;
            }
            $allShopSales = Order::where('shop_id', $currShop->id)->sum('total');
        }

        $dailyBills = count(Order::get());

        return view('home', [
            'user' => $user,
            'dailySells' => $dailySells,
            'allShopSales' => $allShopSales,
            'allDailySales' => $allDailySales,
            'dailyBills' => $dailyBills,
            'orders_count' => $orders->count(),
            'lowStockProducts' => $lowStockProducts,
            'income' => $orders->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count
        ]);
    }
}
