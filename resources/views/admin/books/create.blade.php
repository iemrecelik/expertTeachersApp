@extends('admin.base.index')
@section('contents')

  <books-create-advanced-component
    :pproutes="{ 
      index: '{{ route('admin.books.index') }}',
      advancedStore: '{{ route('admin.books.advancedStore') }}',
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsuccess="'{{ session('succeed') ?? '' }}'"
    :ppimgfilters="{ 
      bookImagesFilt: {{ 
        json_encode(config('imageFilters.filter.bookImagesFilt')) 
      }}
    }"
    :ppoldinput="'{{ json_encode(session()->getOldInput()) }}'"
  >
  </books-create-advanced-component>
@endsection