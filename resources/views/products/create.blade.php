@extends('master')

@section('title')
    Thêm Mới
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

    <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Tên"
                value="{{ old('name') }}" />
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Danh Mục</label>
            <select class="form-select form-select-lg" name="category_id" id="category_id">
                <option selected disabled>Chọn Danh Mục</option>
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}"> {{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">Tags</label>
            <select class="form-select form-select-lg" multiple name="tags[]" id="tags">
                <option selected disabled>Chọn Danh Mục</option>
                @foreach ($tags as $id => $name)
                    <option value="{{ $id }}"> {{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" class="form-control" name="price" id="price" placeholder="Giá"
                value="{{ old('price') }}" />
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô Tả</label>
            <textarea class="form-control" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="image_path" class="form-label">Ảnh</label>
            <input type="file" class="form-control" name="image_path" id="image_path" />
        </div>

        <div class="mb-3">
            <label for="galleries_1" class="form-label">Gallery 1</label>
            <input type="file" class="form-control" name="galleries[]" id="galleries_1" />
        </div>

        <div class="mb-3">
            <label for="galleries_2" class="form-label">Gallery 2</label>
            <input type="file" class="form-control" name="galleries[]" id="galleries_2" />
        </div>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">Quay Lại</a>
        <button type="submit" class="btn btn-success">Thêm Mới</button>
    </form>
@endsection
