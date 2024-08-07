<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Tag;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::with(['category', 'galleries', 'tags'])->latest('id')->paginate(1);

        return view('products.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id')->all();

        $tags = Tag::pluck('name', 'id')->all();

        // dd($categories, $tags);

        return view('products.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $dataProduct = [
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'description' => $request->description
                ];

                if ($request->hasFile('image_path')) {
                    $dataProduct['image_path'] = Storage::put('products', $request->file('image_path'));
                }

                $product = Product::query()->create($dataProduct);

                foreach ($request->galleries as $image) {
                    Gallery::query()->create([
                        'product_id' => $product->id,
                        'image_path' => Storage::put('galleries', $image),
                    ]);
                }

                $product->tags()->attach($request->tags);
            });


            return redirect()->route('products.index')->with('success', 'Thêm thành công');
        } catch (Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['category', 'galleries', 'tags']);

        $productTags = $product->tags->pluck('id')->all();

        $categories = Category::pluck('name', 'id')->all();

        $tags = Tag::pluck('name', 'id')->all();

        return view('products.edit', compact('categories', 'tags', 'product', 'productTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            DB::transaction(function () use ($request, $product) {
                $dataProduct = [
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'description' => $request->description
                ];

                if ($request->hasFile('image_path')) {
                    $dataProduct['image_path'] = Storage::put('products', $request->file('image_path'));
                }

                $product->update($dataProduct);

                foreach ($request->galleries ?? [] as $id => $image) {
                    $gallery = Gallery::findOrFail($id);

                    $gallery->update([
                        'product_id' => $product->id,
                        'image_path' => Storage::put('galleries', $image),
                    ]);
                }

                $product->tags()->sync($request->tags);
            });


            return back()->with('success', 'Cập nhật thành công');
        } catch (Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::transaction(function () use ($product) {
                $product->tags()->sync([]);

                $product->galleries()->delete();

                $product->delete();
            });

            if ($product->image_path && Storage::exists($product->image_path)) {
                Storage::delete($product->image_path);
            }

            // foreach ($product->galleries as $item) {
            //     $item
            // }

            return redirect()->route('products.index')->with('success', 'Xóa thành công');
        } catch (Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }
}
