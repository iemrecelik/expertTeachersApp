@extends('admin.base.index')
@section('contents')
  <books-component
    :pproutes="{ 
      index: '{{ route('admin.books.index') }}', 
      dataList: '{{ route('admin.books.dataList') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppimgfilters="{
      bookImagesFilt: {{ 
        json_encode(config('imageFilters.filter.bookImagesFilt')) 
      }}
    }"
  >
  </books-component>
@endsection