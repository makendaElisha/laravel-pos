<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Setting;
use App\Models\StockMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders = new Order();
        if($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items', 'payments', 'user'])->latest()->paginate(10);

        $total = $orders->map(function($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function($i) {
            return $i->receivedAmount();
        })->sum();

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
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
                'price' => $item['product']['sell_price'] * $item['quantity'],
                'quantity' => $item['quantity'],
                'product_id' => $item['product']['id'],
            ]);
            // $item->save();

            //update stock mouvement
            StockMouvement::create([
                'product_id' => $item['product']['id'],
                'type' => StockMouvement::CREATE_BILL,
                'quantity' => $item['quantity'],
                'user_id' => Auth()->user()->id,
                'shop_id' => $request->shop_id,
            ]);
        }

        return 'success';
    }

    // public function getTotal($cart)
    // {
    //     $total = 0;

    //     foreach ($cart as $item) {
    //         $total += $item->quantity * $item->product->sell_price;
    //     }

    //     return round($total, 2);
    // }

    // public function getDiscount($cart)
    // {
    //     $total = $this->getTotal($cart);
    //     $discountAmount = 0;
    //     $setting = Setting::where('key', 'min_discount_amount')
    //         ->first();


    //     if ($setting && $setting->min_discount_amount && $setting->discount_percentage && $total >= $setting->min_discount_amount)
    //     {
    //         $discountAmount = $total * $setting->discount_percentage / 100;
    //     }

    //     return $discountAmount;
    // }

    // public function getTotalToPay($cart)
    // {
    //     return $this->getTotal($cart) - $this->getDiscount($cart);
    // }
}
