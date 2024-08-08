@extends('master')

@section('title')
    Cập Nhật
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

    <form action="{{ route('products.update', $product) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Tên"
                value="{{ $product->name }}" />
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Danh Mục</label>
            <select class="form-select form-select-lg" name="category_id" id="category_id">
                <option selected disabled>Chọn Danh Mục</option>
                @foreach ($categories as $id => $name)
                    <option @selected($product->category_id == $id) value="{{ $id }}"> {{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">Tags</label>
            <select class="form-select form-select-lg" multiple name="tags[]" id="tags">
                <option selected disabled>Chọn Tags</option>
                @foreach ($tags as $id => $name)
                    <option @selected(in_array($id, $productTags)) value="{{ $id }}"> {{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" class="form-control" name="price" id="price" placeholder="Giá"
                value="{{ $product->price }}" />
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô Tả</label>
            <textarea class="form-control" name="description" id="description" cols="30" rows="10">{{ $product->description }}</textarea>
        </div>

        <div class="mb-3">
            <label for="image_path" class="form-label">Ảnh</label>
            <input type="file" class="form-control" name="image_path" id="image_path" />
            @if ($product->image_path && \Storage::exists($product->image_path))
                <img class="mt-1" src="{{ \Storage::url($product->image_path) }}" width="100px" height="100px"
                    style="object-fit: cover" alt="">
            @endif
        </div>

        @foreach ($product->galleries as $item)
            <div class="mb-3">
                <label for="galleries_{{ $loop->iteration }}" class="form-label">Gallery {{ $loop->iteration }}</label>
                <input type="file" class="form-control" name="galleries[{{ $item->id }}]"
                    id="galleries_{{ $loop->iteration }}" />

                @if ($item->image_path && \Storage::exists($item->image_path))
                    <img class="mt-1" src="{{ \Storage::url($item->image_path) }}" width="100px" height="100px"
                        style="object-fit: cover" alt="">
                @endif
            </div>
        @endforeach

        <div class="mt-3">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Quay Lại</a>
            <button type="submit" class="btn btn-success">Cập Nhật</button>
        </div>
    </form>
@endsection
