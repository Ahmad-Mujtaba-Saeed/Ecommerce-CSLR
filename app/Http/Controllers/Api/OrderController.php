<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ShippingAddress;

class OrderController extends Controller
{

    public function getUserOrders(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('orderProducts') // eager load related products
            ->where('buyer_id', $user->id)
            ->where('buyer_type', 'customer') // adjust if needed
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }



    public function createOrder(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|string|exists:cart_products,cart_id',
            'user_id' => 'required|integer|exists:users,id',
            'buyer_type' => 'required|string',
            'address' => 'required|array',
            'address.first_name' => 'required|string',
            'address.last_name' => 'required|string',
            'address.email' => 'required|email',
            'address.phone_number' => 'required|string',
            'address.address' => 'required|string',
            'address.city' => 'required|string',
            'address.zip_code' => 'required|string',
            'address.state_id' => 'required|integer',
            'address.country_id' => 'required|integer',
            'payment_method' => 'required|in:stripe,paypal,cod',
            'payment_credentials' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $cart = Cart::where('cart_id', $request->cart_id)->firstOrFail();
            $products = $cart->products_data;

            if (empty($products)) {
                return response()->json(['message' => 'Cart is empty.'], 400);
            }

            // 1. Calculate totals
            $subtotal = collect($products)->sum('total_price');
            $shipping_cost = 0; // For now, hardcoded
            $total = $subtotal + $shipping_cost;

            // 2. Handle payment
            $paymentStatus = 'pending';
            if ($request->payment_method !== 'cod') {
                $paymentStatus = $this->processPaymentBackend(
                    $request->payment_method,
                    $request->payment_credentials ?? [],
                    $total
                );

                if ($paymentStatus !== 'success') {
                    return response()->json(['message' => 'Payment failed'], 402);
                }
            } else {
                $paymentStatus = 'cod_pending';
            }

            // 3. Create Order
            $order = Order::create([
                'order_number' => null, // temporarily null
                'buyer_id' => $request->user_id,
                'buyer_type' => $request->buyer_type,
                'price_subtotal' => $subtotal,
                'price_shipping' => $shipping_cost,
                'price_total' => $total,
                'price_currency' => 'USD',
                'status' => 1,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
            ]);

            // Set order number as id + offset (e.g., 10000)
            $order->order_number = 10000 + $order->id;
            $order->save();

            // 4. Save Order Products
            foreach ($products as $product) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'seller_id' => $product['seller_id'] ?? null,
                    'buyer_id' => $request->user_id,
                    'buyer_type' => $request->buyer_type,
                    'product_id' => $product['product_id'],
                    'product_title' => $product['product_name'],
                    'product_slug' => $product['product_name'] ?? null,
                    'product_unit_price' => $product['unit_price'],
                    'product_quantity' => $product['quantity'],
                    'product_currency' => 'USD',
                    'product_total_price' => $product['total_price'],
                    'product_type' => 'physical',
                    'order_status' => 'pending',
                ]);
            }

            // 5. Save Shipping Address
            $addr = $request->address;
            ShippingAddress::create([
                'user_id' => $request->user_id,
                'title' => $addr['first_name'] . ' ' . $addr['last_name'],
                'first_name' => $addr['first_name'],
                'last_name' => $addr['last_name'],
                'email' => $addr['email'],
                'phone_number' => $addr['phone_number'],
                'address' => $addr['address'],
                'city' => $addr['city'],
                'zip_code' => $addr['zip_code'],
                'state_id' => $addr['state_id'],
                'country_id' => $addr['country_id'],
                'address_type' => 'shipping',
            ]);

            // 6. Clear cart
            $cart->update(['products_data' => null]);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => $paymentStatus,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function processPaymentBackend($method, $credentials, $amount)
    {
        if ($method === 'stripe') {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $charge = \Stripe\Charge::create([
                    'amount' => $amount * 100,
                    'currency' => 'USD',
                    'description' => 'Order Payment',
                    'source' => $credentials['token'] ?? null,
                ]);

                return $charge->status === 'succeeded' ? 'success' : 'failed';

            } catch (\Exception $e) {
                return 'failed';
            }
        }

        if ($method === 'paypal') {
            // Assume success for now
            return 'success';
        }

        return 'failed';
    }
}
