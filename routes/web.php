<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('assets/{path}', function ($path) {
    return response()->file(public_path("assets/$path"));
});

Auth::routes();

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('/settings/get-discount', [SettingController::class, 'getDiscount'])->name('settings.get.discount');
    Route::get('/settings/get-shop/{id}', [SettingController::class, 'getShop'])->name('settings.get.shop');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    Route::get('shop-items/{shop}/products', [ProductController::class, 'getProducts'])->name('products.index');
    Route::resource('products', ProductController::class);
    Route::get('product/{product}/assign-shop', [ProductController::class, 'assignProducts'])->name('assign.products');
    Route::post('product/assign-shop', [ProductController::class, 'saveAssignProducts'])->name('save.assign.products');
    Route::get('/transfer/list', [ProductController::class, 'shopsStockMovements'])->name('shops.stock.movements');
    Route::get('/historic/articles', [ProductController::class, 'stockMouvement'])->name('article.global.movements');


    Route::get('/shop/{shop}/products', [ShopController::class, 'index'])->name('shop.products.index');
    Route::resource('customers', CustomerController::class);
    Route::post('/cart-orders', [OrderController::class, 'storeCart'])->name('shop.cart.store');
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/item/{orderItem}/delete', [OrderController::class, 'destroySingle']);
    Route::get('/orders/all/deleted', [OrderController::class, 'deletedOrders'])->name('orders.deleted.index');
    Route::get('/current/reprint/order/{order}', [OrderController::class, 'getOrderContent'])->name('reprint.getOrderContent');

    //To be removed
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/shop/{shop}/cart', [CartController::class, 'index'])->name('shop.cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);
    Route::get('/reprint/order/{order}', [CartController::class, 'reprint'])->name('reprint.shop.order');

    //Dashboard
    Route::post('/{id}', [HomeController::class, 'seen'])->name('home.seen');

    //PDF
    Route::get('pdf/store/generate-pdf', [PDFController::class, 'generatePDF'])->name('products.list.pdf');
    Route::get('pdf/store/generate-pdf/{shop}', [PDFController::class, 'generateShopPDF'])->name('products.shop.list.pdf');
    Route::get('pdf/store/generate-bills-pdf', [PDFController::class, 'generateBillsPDF'])->name('orders.list.pdf');
});
