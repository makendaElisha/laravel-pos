<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\UpdatedStock;
use Carbon\Carbon;
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
        $today = Carbon::today();
        $user = Auth()->user();
        $lushi = Shop::where('name', Shop::LUBUMBASHI)->first();
        $kolwezi = Shop::where('name', Shop::KOLWEZI)->first();
        $kilwa = Shop::where('name', Shop::KILWA)->first();

        $orders = Order::with(['items', 'payments'])->where('created_at', '>=', date('Y-m-d').' 00:00:00')->get();
        $customers_count = Customer::count();

        $dailySells = Order::where('created_at', '>=', date('Y-m-d').' 00:00:00')->sum('total');
        $dailySellsDiscount = Order::where('created_at', '>=', date('Y-m-d').' 00:00:00')->sum('discount');
        $dailySellsAfterDiscount = $dailySells - $dailySellsDiscount;

        $allDailySales = [
            Shop::LUBUMBASHI => $lushi ? Order::where('shop_id', $lushi->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00') : '',
            Shop::KILWA => $kolwezi ? Order::where('shop_id', $kilwa->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00') : '',
            Shop::KOLWEZI => $kilwa ? Order::where('shop_id', $kolwezi->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00') : '',
        ];

        $lowStockProducts = Product::whereColumn('quantity', '<', 'min_quantity')->get();

        $allShopSales = 0;
        $allShopSalesDiscount = 0;
        $allShopSalesAfterDiscount = 0;
        $shopProducts = [];
        if (!$user->is_admin) {
            $currShop = Shop::where('name', $user->shop_name)->first();
            $dailySells = Order::where('shop_id', $currShop->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00')->sum('total');

            $ids = [];
            $shpProds = ShopProduct::with('product')->where('shop_id', $currShop->id)->get();
            foreach ($shpProds as $prodItem) {
                if ($prodItem->product && $prodItem->quantity < $prodItem->product->min_quantity) {
                    $ids[] = $prodItem->product_id;
                }
            }

            $lowStockProducts = Product::whereIn('id', $ids)->get();
            foreach ($lowStockProducts as $prod) {
                $prod->shop_quantity = $shpProds->where('product_id', $prod->id)->first()->quantity;
            }
            $allShopSales = Order::where('shop_id', $currShop->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00')->sum('total');
            $allShopSalesDiscount = Order::where('shop_id', $currShop->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00')->sum('discount');
            $allShopSalesAfterDiscount = $allShopSales - $allShopSalesDiscount;

            // updated stock
            $shopProducts = UpdatedStock::with('product')
                ->where('shop_id', $currShop->id)
                ->whereNull('seen_by')
                ->whereHas('product', function ($query) {
                    // Add a condition to check if the product exists
                    $query->whereNotNull('id');
                })
                ->get();
        }

        $dailyBills = count(Order::where('created_at', '>=', date('Y-m-d').' 00:00:00')->get());


        return view('home', [
            'user' => $user,
            'shopProducts' => $shopProducts,
            'dailySells' => $dailySells,
            'dailySellsDiscount' => $dailySellsDiscount,
            'dailySellsAfterDiscount' => $dailySellsAfterDiscount,
            'allShopSales' => $allShopSales,
            'allShopSalesDiscount' => $allShopSalesDiscount,
            'allShopSalesAfterDiscount' => $allShopSalesAfterDiscount,
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

    public function seen(Request $request) {
        $id = $request->id;

        if ($id) {
            $record = UpdatedStock::where('id', $id)->first();
            $record->seen_by = Auth()->user()->id;
            $record->save();
        }
        return redirect()->back();
    }
}
