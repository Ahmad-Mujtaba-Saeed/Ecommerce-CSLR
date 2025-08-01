<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::forCurrentUser()->with(['product.details', 'product.licenseKeys', 'product.searchIndexes', 'product.category', 'product.user', 'product.images', 'product.variations', 'product.defaultVariationOptions', 'product.mainImage'])->get();
        return response()->json($cartItems);
    }

    public function add(Request $request)
    {
        $cartItem = Cart::addOrUpdateItem($request->all());
        return response()->json($cartItem);
    }

    public function remove($cartItemId)
    {
        $cartItem = Cart::forCurrentUser()->where('cart_item_id', $cartItemId)->first();
        $cartItem->delete();
        return response()->json($cartItem);
    }
}