<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
class ProductController extends Controller
{
    public function ProductList(Request $request)
    {
        $product = Product::with(['details', 'licenseKeys', 'searchIndexes', 'category', 'user', 'images', 'variations', 'defaultVariationOptions', 'mainImage'])->paginate(10);
        return response()->json($product);
    }

    public function getSpecialOfferProducts()
    {
        $product = Product::with(['details', 'licenseKeys', 'searchIndexes', 'category', 'user', 'images', 'variations', 'defaultVariationOptions', 'mainImage'])
        ->specialOffers()
        ->orderBy('special_offer_date', 'DESC')
        ->paginate(10);
        return response()->json($product);
    }

    public function getPromotedProducts()
    {
        $product = Product::with(['details', 'licenseKeys', 'searchIndexes', 'category', 'user', 'images', 'variations', 'defaultVariationOptions', 'mainImage'])
        ->promoted()
        ->paginate(10);
        return response()->json($product);
    }

    public function getProductsByCategory($category_id)
    {
        $product = Product::with(['details', 'licenseKeys', 'searchIndexes', 'category', 'user', 'images', 'variations', 'defaultVariationOptions', 'mainImage'])
        ->where('category_id', $category_id)
        ->paginate(10);
        return response()->json($product);
    }


}
