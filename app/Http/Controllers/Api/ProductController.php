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
        $productId = $request->query('product_id');

        if ($productId) {
            $product = Product::with([
                'details', 'licenseKeys', 'searchIndexes', 'category',
                'user', 'images', 'variations', 'defaultVariationOptions', 'mainImage'
            ])->find($productId);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }

        // Otherwise return paginated list
        $products = Product::with([
            'details', 'licenseKeys', 'searchIndexes', 'category',
            'user', 'images', 'variations', 'defaultVariationOptions', 'mainImage'
        ])->paginate(10);

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
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

    public function search(Request $request)
    {
        $keyword = $request->query('keyword');

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword is required'
            ], 400);
        }

        $products = Product::whereHas('details', function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%");
            })
            ->with([
                'details', 'licenseKeys', 'searchIndexes', 'category',
                'user', 'images', 'variations', 'defaultVariationOptions', 'mainImage'
            ])
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }




}
