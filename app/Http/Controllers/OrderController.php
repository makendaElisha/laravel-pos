<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\StockMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request) {
        $user = Auth()->user();
        $orders = new Order();
        $shops = Shop::get();

        $shopId = '0';
        if (!$user->is_admin) {
            $shopId = Shop::where('name', $user->shop_name)->first()->id;
            $orders = $orders->where('shop_id', $shopId);
        }

        if( $user->is_admin && $request->shop) {
            $orders = $orders->where('shop_id', $request->shop);
            $shopId = $request->shop;
        }

        if($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }

        if($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $total = $orders->sum('total');

        $orders = $orders->with(['items', 'payments', 'user'])->latest()->paginate(10);

        // $total = $orders->map(function($i) {
        //     return $i->total();
        // })->sum();

        return view('orders.index', compact('orders',
            'user',
            'total',
            'shopId',
            'shops'
        ));
    }

    public function storeCart(OrderStoreRequest $request)
    {
        $invNumber = sprintf('%06d', DB::table('orders')->max('order_number'));

        $order = Order::create([
            'order_number' => $invNumber ? $invNumber + 1 : 1,
            'shop_id' => $request->shop_id,
            'customer' => $request->customer,
            'user_id' => $request->user()->id,
            'total' => $request->total,
            'discount' => $request->discount,
            'paid' => $request->paid,
        ]);

        $cart = $request->cart;
        foreach ($cart as $item) {
            $order->items()->create([
                'price' => $item['unit'] == $item['product']['sell_price'] * $item['final_quantity'],
                'quantity' => $item['final_quantity'],
                'product_id' => $item['product']['id'],
            ]);

            //update stock mouvement
            StockMouvement::create([
                'product_id' => $item['product']['id'],
                'type' => StockMouvement::CREATE_BILL,
                'quantity' => $item['final_quantity'],
                'user_id' => Auth()->user()->id,
                'shop_id' => $request->shop_id,
            ]);
        }

        return 'success';
    }
}
