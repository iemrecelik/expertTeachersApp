@extends('admin.base.index')
@section('contents')

  <books-edit-advanced-component
    :pproutes="{ 
      index: '{{ route('admin.books.index') }}', 
      dataList: '{{ route('admin.books.dataList') }}',
    }"
    :ppitem="{{ $item }}"
    :ppimgs="{{ $item->images }}"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsuccess="'{{ session('succeed') ?? '' }}'"
    :ppimgfilters="{ 
      bookImagesFilt: {{ 
        json_encode(config('imageFilters.filter.bookImagesFilt')) 
      }}
    }"
    :ppoldinput="'{{ json_encode(session()->getOldInput()) }}'"
  >
  </books-edit-advanced-component>
@endsection