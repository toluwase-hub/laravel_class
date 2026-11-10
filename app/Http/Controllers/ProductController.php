<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

use function Pest\Laravel\get;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $per_page =  $request->input('per_page');
            $products = Product::latest()->with('size');

            $products = $products->paginate($per_page ?? 10);

            return response()->json([
                'message' => 'success',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::info('error while creating product' . $e);
            return response()->json([
                'message' => 'error while creating product',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validateData = $request->validated();
        try {

            $product = Product::create(
                $validateData
            );

            foreach ($validateData['size'] as $size) {
                $sizes = Size::create(['size' => $size['size'], 'in_stock' => $size['in_stock'], 'product_id' => $product->id]);
            };


            $product->refresh();

            return response()->json([
                'message' => 'creating product was a success',
                'data' => [
                    $product->load('size')
                ],
            ]);
        } catch (\Exception $e) {
            Log::info('error while creating product' . $e);
            return response()->json([
                'message' => 'error while creating product',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'No product found',
                'data' => null
            ]);
        }

        return response()->json([
            'message' => '',
            'data' => $product->load('size')
        ]);
    }

    public function uploadImage(Request $request, String $id)
    {

        $request->validate([
            'image' => 'required|mimes:jpeg,png',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'No product found',
                'data' => null
            ]);
        }

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('/products', 'public');
                $product->image = $imagePath;
                $product->save();
            }

            $product->refresh();

            return response()->json([
                'message' => 'upload product image was a success',
                'data' => [
                    $product->load('size')
                ],
            ]);
        } catch (\Exception $e) {
            Log::info('error while image product' . $e);
            return response()->json([
                'message' => 'error while image product',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, String $id)
    {

        $validateData = $request->validated();

        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'No product found',
                    'data' => null
                ]);
            }

            $product->update($validateData);

            $product->refresh();

            return response()->json([
                'message' => 'Updating product was a success',
                'data' => [
                    $product->load('size')
                ],
            ]);
        } catch (\Exception $e) {
            Log::info('error while creating product' . $e);
            return response()->json([
                'message' => 'error while creating product',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Search the specified size in storage.
     */
    public function searchProduct(Request $request)
    {
        $searchInput = $request->input('searchInput');
        try {
            $product = Product::where('name', 'LIKE', "%{$searchInput}%")->with('size')->paginate(5);
            if (!$product || empty($searchInput)) {
                return response()->json([
                    'message' => 'No product found',
                    'data' => null
                ]);
            }

            return response()->json([
                'message' => 'Search result: ' .  $searchInput,
                'data' => [
                    $product
                ]
            ]);
        } catch (\Exception $e) {
            Log::info('Error while searching product' . $e);
            return response()->json([
                'message' => 'Error while searching product ' . $e,
                'error' => $e
            ], 500);
        }
    }

    /**
     * Filter the specified resource in storage.
     */
    public function filterProduct(string $category)
    {
        $product = Product::where('category', "{$category}")->with('size')->paginate(5);

        try {

            if (!$product) {
                return response()->json([
                    'message' => 'No category found',
                    'data' => null
                ]);
            }

            return response()->json([
                'message' => 'Search result: ' .  $category,
                'data' => [
                    $product
                ]
            ]);
        } catch (\Exception $e) {
            Log::info('Error while searching product' . $e);
            return response()->json([
                'message' => 'Error while searching product' . $e,
                'error' => $e
            ], 500);
        }
    }

    /**
     * Update the specified size in storage.
     */
    public  function updatesize(UpdateProductRequest  $request, string $id)
    {
        $validateData = $request->validated();

        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'message' => 'No product found',
                    'data' => null,
                ]);
            }
            foreach ($validateData['size'] as $size) {
                $existSize = Size::where('product_id', $id)->where('size', $size['size'])->first();
                if ($existSize) {
                    if ($existSize['in_stock'] === null) {
                        $existSize['in_stock'] = $size['in_stock'];
                    } else {
                        $existSize['in_stock'] += $size['in_stock'];
                        $existSize->save();
                    }
                } else {
                    $product->size()->Create(
                        [
                            'size' => $size['size'],
                            'in_stock' => $size['in_stock']
                        ]
                    );
                }
            }
            $product->refresh();
            return response()->json([
                'message' => 'Updating size successful',
                'data' => [
                    $product->load('size')

                ],
            ]);
        } catch (\Exception $e) {
            Log::info('error while creating product' . $e);
            return response()->json([
                'message' => 'error while creating product',
                'error' => $e,
            ], 500);
        }
    }

    /**
     * Remove the specified size from storage.
     */
    public function deleteSize(Request  $request, string $id)
    {
        $validateData = $request->validate([
            'size' => 'array',
            'size.*' => 'required|exists:sizes,id'
        ], [
            'size.*.exists' => 'one or more seleted sizes does not exist'
        ]);

        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'mesage' => 'No Product found',
                    'data' => null
                ]);
            }
            foreach ($validateData['size'] as $size) {
                $sizes = Size::where('id', $size)->where('product_id', $id)->first();
                if (!$sizes) {
                    return response()->json([
                        'message' => 'size does not exist for this product',
                        'data' => null
                    ]);
                }
                $sizes->delete();
            }
            $product->refresh();
            return response()->json([
                'message' => 'Product sizes deleted successful',
                'data' => [
                    $product->load('size')
                ]
            ]);
        } catch (\Exception $e) {
            Log::info('error while deleting product size' . $e);
            return response()->json([
                'message' => 'error while deleting product size',
                'error' => $e
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'No product found',
                'data' => null
            ]);
        }

        $product->delete();

        return response()->json([
            'message' => '',
            'data' => null
        ]);
    }
};
