<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request)
    {
        $validateData = $request->validated();

        try {
            $product = Product::find($validateData['product_id']);
            if (!$product) {
                return response()->json([
                    'message' => 'No product found',
                    'data' => null,
                ]);
            }
            $sizes = Size::where('id', $validateData['size_id'])->where('product_id', $validateData['product_id'])->first();
            if (!$sizes) {
                return response()->json([
                    'message' => 'size does not exist for this product',
                    'data' => null
                ]);
            }
            $existItem = Cart::where('session_id', $validateData['session_id'])->where('product_id', $validateData['product_id'])->where('size_id', $validateData['size_id'])->first();
            if ($existItem) {
                $existItem['quantity'] += $validateData['quantity'];
                $existItem->save();
            } else {
                $existItem =  Cart::create([
                    'session_id' => Str::uuid(),
                    'product_id' => $validateData['product_id'],
                    'size_id' =>  $validateData['size_id'],
                    'quantity' =>  $validateData['quantity'],
                ]);
            }
            $existItem->refresh();
            return response()->json([
                'massage' => 'Product added to cart successful',
                'data' => [
                    $existItem
                ]
            ]);
        } catch (\Exception $e) {
            Log::info('error while adding product to cart' . $e);

            return response()->json([
                'massage' => 'error while adding product to cart ',
                'error' => $e
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cart = Cart::where('session_id', $id)->paginate(5);
        if (!$cart) {
            return response()->json([
                'message' => "Cart not found",
                'data' => 'null'
            ]);
        }
        return response()->json([
            'message' => 'cart fetch successful',
            'data' => [
                $cart
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validateData = $request->validate([
            'session_id' => 'required',
            'cart_id' => 'required|exists:carts,id'
        ], ['cart_id.exists' => 'no cart found for this user']);
        try {
            $cart = Cart::where('session_id', $validateData['session_id'])->where('id', $validateData['cart_id']);
            if (!$cart) {
                return response()->json([
                    'message' => 'no cart found',
                    'data' => null
                ]);
            }
            $cart->delete();
            return response()->json([
                'message' => 'item deleted successful'
            ]);
        } catch (\Exception $e) {
            Log::info('error while creating product' . $e);
            return response()->json([
                'message' => 'error while creating product',
                'error' => $e,
            ], 500);
        }
    }
}
