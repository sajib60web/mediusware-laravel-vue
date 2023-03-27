<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('productVariants', 'productVariantPrice')
            ->when(request()->get('title'), function (Builder $builder) {
                $builder->where('title', 'LIKE', '%' . request()->get('title') . '%');
            })->when(request()->get('variant'), function (Builder $builder) {
                $builder->whereHas('productVariants', function (Builder $builder) {
                    $builder->where('variant', 'LIKE', '%' . request()->get('variant') . '%');
                });
            })->when(request()->get('price_from'), function (Builder $builder) {
                $builder->whereHas('productVariantPrice', function (Builder $builder) {
                    $builder->whereBetween('price', [request()->get('price_from'), request()->get('price_to')]);
                });
            })->when(request()->get('date'), function (Builder $builder) {
                $builder->whereDate('created_at', '=', Carbon::parse(request()->get('date'))->format('Y-m-d'));
            })->paginate(5);

        $variants = Variant::all();
        return view('products.index', compact('products', 'variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255|unique:products,title',
            'sku' => 'required|max:255|unique:products,sku',
        ]);

        $product = Product::create($request->only('title', 'sku', 'description'));

        if ($request->hasfile('product_image')) {
            $this->uploadImage($request->file('product_image'), $product);
        }
        if ($request->product_variant) {
            $this->productVariant($request->product_variant, $product);
        }

        if ($request->product_variant_prices) {
            $this->productVariantPrice($request->product_variant_prices, $product);
        }
        return response()->json(['success' => 'Product created successfully'], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $productEdit = $product->load(['images', 'productVariants', 'productVariantPrice' => function ($query) {
            $query->with('variantNameOne', 'variantNameTwo', 'variantNameThree');
        }]);

        $variants = Variant::all();
        return view('products.edit', compact('variants', 'productEdit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|max:255|unique:products,title,'.$product->id,
            'sku' => 'required|max:255|unique:products,sku,'.$product->id,
        ]);

        $product->update($request->only('title', 'sku', 'description'));

        if ($request->hasfile('product_image')) {
            $this->uploadImage($request->file('product_image'), $product);
        }

        if ($request->product_variant) {
            $product->productVariants()->delete();
            $this->productVariant($request->product_variant, $product);
        }

        if ($request->product_variant_prices) {
            $product->productVariantPrice()->delete();
            $this->productVariantPrice($request->product_variant_prices, $product);
        }
        return response()->json(['success' => 'Product updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function uploadImage($request, $product)
    {
        foreach ($request as $image) {
            $extension = uniqid() . '.' . $image->getClientOriginalExtension();
            $filePath = 'images/';
            $image->move(public_path($filePath), $extension);
            $imagePath = $filePath . $extension;
            $product->images()->updateOrCreate([
                'file_path' => $imagePath
            ]);
        }
    }

    public function productVariant($request, $product)
    {
        foreach ($request as $items) {
            $tags = explode(",", $items['tags']);
            foreach ($tags as $tag) {
                $product->productVariants()->updateOrCreate([
                    'variant' => $tag,
                    'variant_id' => $items['option'],
                ]);
            }
        }
    }

    public function productVariantPrice($request, $product)
    {
        $variants = $product->load('productVariants')->productVariants->pluck('id', 'variant');
        foreach ($request as $item) {
            $titles = explode('/', $item['title']);
            $productVariants = [];
            foreach (['one', 'two', 'three'] as $k => $value) {
                $id = isset($titles[$k]) ? $titles[$k] : null;
                if ($id) {
                    $productVariants["product_variant_$value"] = $variants[$id];
                }
            }
            $product->productVariantPrice()->updateOrCreate(array_merge($productVariants, [
                'price' => $item['price'],
                'stock' => $item['stock'],
            ]));
        }
    }
}
