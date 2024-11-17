<?php

use App\Http\Controllers\ProductQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('get-quantity/shop/{shop}/product/{product}', [ProductQuery::class, 'getShopProductQuantity'])->name('get.shop.product.quantity');
Route::post('set-quantity/shop', [ProductQuery::class, 'setShopProductQuantity'])->name('set.shop.product.quantity');
Route::post('set-quantity-petit-depot/shop', [ProductQuery::class, 'setShopProductQuantityPetitDepot'])->name('set.shop.product.quantity.petit');
Route::post('send-quantity-petit-depot/shop', [ProductQuery::class, 'sendShopProductQuantityPetitDepot'])->name('set.shop.product.quantity.petit');
Route::post('set-quantity/store', [ProductQuery::class, 'setStoreProductQuantity'])->name('set.store.product.quantity');
Route::post('create-product-shop/{shop}/product/{product}', [ProductQuery::class, 'createProductInShop'])->name('create.shop.product');
