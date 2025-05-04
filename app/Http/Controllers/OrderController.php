<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\StockMouvement;
use App\Models\UpdatedStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request) {
        $user = Auth()->user();
        $orders = Order::with(['items.product', 'user']);
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

        if($request->search) {
            $orders = $orders->where('order_number', $request->search);
        }

        $total = $orders->sum('paid');

        //Get query parameters and append to pagination
        $queries = $request->query();
        $orders = $orders->with(['items', 'payments', 'user'])
            ->latest()
            ->paginate(10)
            ->appends($queries);

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

    public function deletedOrders(Request $request) {
        $user = Auth()->user();
        $orders = Order::with(['items.product', 'user'])->onlyTrashed();
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

        if($request->search) {
            $orders = $orders->where('order_number', $request->search);
        }

        $total = $orders->sum('paid');

        //Get query parameters and append to pagination
        $queries = $request->query();
        $orders = $orders->with(['items', 'payments', 'user'])
            ->latest()
            ->paginate(10)
            ->appends($queries);

        return view('orders.deletedIndex', compact('orders',
            'user',
            'total',
            'shopId',
            'shops'
        ));
    }

    public function storeCart(OrderStoreRequest $request)
    {
        $invNumber = DB::table('orders')
            ->select(DB::raw('MAX(CAST(order_number AS SIGNED)) as max_order_number'))
            ->value('max_order_number');

        $order = Order::create([
            'order_number' => $invNumber ? $invNumber + 1 : 1,
            'shop_id' => $request->shop_id,
            'customer' => $request->customer,
            'phone' => $request->phone,
            'user_id' => $request->user()->id,
            'total' => $request->total,
            'discount' => $request->discount,
            'paid' => $request->paid,
        ]);

        $cart = $request->cart;
        foreach ($cart as $item) {
            $order->items()->create([
                'price' => $item['sell_price'],
                'quantity' => $item['final_quantity'],
                'product_id' => $item['product']['id'],
            ]);

            $shopProd = ShopProduct::where('shop_id', $request->shop_id)
                ->where('product_id', $item['product']['id'])
                ->first();

            $qtyBefore = $shopProd->quantity;
            $qtyAfter = null;

            // Deduct form shop
            $shopProd->quantity -= (int) $item['final_quantity'];
            $shopProd->save();

            $qtyAfter = $shopProd->quantity;

            //update stock mouvement
            StockMouvement::create([
                'product_id' => $item['product']['id'],
                'type' => StockMouvement::CREATE_BILL,
                'quantity' => $item['final_quantity'],
                'user_id' => Auth()->user()->id,
                'shop_id' => $request->shop_id,
                'quantity_before' => $qtyBefore,
                'quantity_after' => $qtyAfter,
            ]);
        }

        return response()->json([
            'order' => Order::with(['items.product', 'user'])->where('id', $order->id)->first(),
        ], 200);
    }

    public function getOrderContent(Order $order)
    {
        return response()->json([
            'order' => Order::with(['items.product', 'user'])->where('id', $order->id)->first(),
        ], 200);
    }

    public function destroy(Order $order)
    {
        $keepOrderItems = $order->items;

        if ($order->items()->delete()) {

            //Return stock
            foreach ($keepOrderItems as $orderItem) {
                $shopProd = ShopProduct::where('shop_id', $order->shop_id)
                    ->where('product_id', $orderItem->product_id)
                    ->first();

                $qtyBefore = $shopProd->quantity;
                $qtyAfter = null;

                $shopProd->quantity += (int) $orderItem->quantity;
                $shopProd->save();

                $qtyAfter = $shopProd->quantity;

                // update stock mouvement
                StockMouvement::create([
                    'product_id' => $shopProd->product_id ?? null,
                    'type' => StockMouvement::CANCEL_BILL,
                    'quantity' => $orderItem->quantity,
                    'user_id' => Auth()->user()->id,
                    'shop_id' => $order->shop_id,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $qtyAfter,
                ]);
            }
        }

        $order->delete();

        return redirect()->route('orders.index');
    }

    public function destroySingle(Order $order, OrderItem $orderItem)
    {
        $itemAmount = $orderItem->price;
        $itemQuantity = $orderItem->quantity;

        if ($orderItem->delete()) {
            $newTotal= $order->total - $itemAmount * $itemQuantity;
            $discount = $this->getDiscount($newTotal);

            $order->total = $newTotal;
            $order->paid = $newTotal - $discount;
            $order->discount = $discount;
            $order->save();

            // Restore stock
            $shopProd = ShopProduct::where('shop_id', $order->shop_id)
                ->where('product_id', $orderItem->product_id)
                ->first();

            $qtyBefore = $shopProd->quantity;
            $qtyAfter = null;

            $shopProd->quantity += (int) $itemQuantity;
            $shopProd->save();

            $qtyAfter = $shopProd->quantity;

            StockMouvement::create([
                'product_id' => $shopProd->product_id ?? null,
                'type' => StockMouvement::CANCEL_BILL,
                'quantity' => $itemQuantity,
                'user_id' => Auth()->user()->id,
                'shop_id' => $order->shop_id,
                'quantity_before' => $qtyBefore,
                'quantity_after' => $qtyAfter,
            ]);
        }

        return redirect()->route('orders.index');
    }

    public function getDiscount($total) {
        $discountAmount = 0;
        $dbDiscount = (Setting::where('key', 'min_discount_amount')->first())->value ?? null;
        $dbPercent = (Setting::where('key', 'discount_percentage')->first())->value ?? null;

        if ($dbDiscount && $dbPercent && $total >= $dbDiscount) {
            $discountAmount = $total * $dbPercent / 100;
        }

        return $discountAmount ?? 0;
    }
}
