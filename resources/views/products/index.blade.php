@extends('master')

@section('title')
    Danh Sách
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif

    <a href="{{ route('products.create') }}" class="btn btn-success">Thêm Mới</a>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Tên Sản Phẩm</th>
                <th scope="col">Danh Mục</th>
                <th scope="col">Tags</th>
                <th scope="col">Giá</th>
                <th scope="col">Mô Tả</th>
                <th scope="col">Ảnh</th>
                <th scope="col">Gallery</th>
                <th scope="col">Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($data as $product)
                    <th scope="row">{{ $product->id }}</th>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>

                    <td>
                        @foreach ($product->tags as $tag)
                            <span class="badge bg-info">{{ $tag->name }}</span>
                        @endforeach
                    </td>

                    <td>{{ number_format($product->price) }}</td>
                    <td>{{ $product->description }}</td>

                    <td>
                        @if ($product->image_path && \Storage::exists($product->image_path))
                            <img src="{{ \Storage::url($product->image_path) }}" width="100px" height="100px"
                                style="object-fit: cover" alt="">
                        @endif
                    </td>

                    <td>
                        @if ($product->image_path && \Storage::exists($product->image_path))
                            <img src="{{ \Storage::url($product->image_path) }}" width="100px" height="100px"
                                style="object-fit: cover" alt="">
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Sửa</a>
                        <form action="{{ route('products.destroy', $product) }}" method="post" style="display: inline;">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Bạn có muốn xóa không')">Xóa</button>
                        </form>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    {{ $data->links() }}
@endsection
