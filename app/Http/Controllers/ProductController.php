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
    public function index()
    {
        // Lấy dữ liệu category,galleries và tags trong relation ở model để hiển thị ra index:
        $data = Product::with(['category', 'galleries', 'tags'])->latest('id')->paginate(1);

        return view('products.index', compact('data'));
    }

    public function create()
    {
        // Lấy dữ liệu id và name của Category để hiển thị ra form:
        $categories = Category::pluck('name', 'id')->all();

        // Lấy dữ liệu id và name của Tag để hiển thị ra form:
        $tags = Tag::pluck('name', 'id')->all();

        // dd($categories, $tags);

        return view('products.create', compact('categories', 'tags'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Thêm dữ liệu nhập tay input:
                $dataProduct = [
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'description' => $request->description
                ];

                // Thêm ảnh vào db products:
                if ($request->hasFile('image_path')) {
                    $dataProduct['image_path'] = Storage::put('products', $request->file('image_path'));
                }

                // Chạy các câu lệnh thêm ở trên:
                $product = Product::query()->create($dataProduct);

                // Thêm nhiều ảnh vào galleries:
                foreach ($request->galleries as $image) {
                    Gallery::query()->create([
                        'product_id' => $product->id,
                        'image_path' => Storage::put('galleries', $image),
                    ]);
                }

                // Thêm tags:
                $product->tags()->attach($request->tags);
            });


            // redirect về view và trả ra session phản hồi:
            return redirect()->route('products.index')->with('success', 'Thêm thành công');
        } catch (Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        // Copy từ index và xóa bỏ model, thêm biến và ->load, xóa bỏ latest các thứ:
        $product->load(['category', 'galleries', 'tags']);

        $categories = Category::pluck('name', 'id')->all();

        $tags = Tag::pluck('name', 'id')->all();

        // Thêm productTags có thể copy từ dòng trên để sửa, bỏ 'name':
        $productTags = $product->tags->pluck('id')->all();

        // Sửa view và các giả trị:
        return view('products.edit', compact('categories', 'tags', 'product', 'productTags'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        // Copy từ store:
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

                // Xóa bỏ model và sửa từ create sang update
                $product->update($dataProduct);

                // Thêm ?? [] và $is =>
                foreach ($request->galleries ?? [] as $id => $image) {
                    // Thêm biến và model findOrFail
                    $gallery = Gallery::findOrFail($id);

                    // Xóa bỏ model và sửa từ create sang update
                    $gallery->update([
                        'product_id' => $product->id,
                        'image_path' => Storage::put('galleries', $image),
                    ]);
                }

                // Chuyển từ attach thành sync
                $product->tags()->sync($request->tags);
            });

            // Sửa lại câu session trả về
            return back()->with('success', 'Cập nhật thành công');
        } catch (Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    public function destroy(Product $product)
    {
        // Copy form try catch xuống và xóa dữ liệu bên trong trừ DB::transaction
        try {
            DB::transaction(function () use ($product) {
                // Làm trống tags - Xóa tags
                $product->tags()->sync([]);

                // Xóa galleries
                $product->galleries()->delete();

                // Xóa product
                $product->delete();
            });

            // Xóa ảnh trong product
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
