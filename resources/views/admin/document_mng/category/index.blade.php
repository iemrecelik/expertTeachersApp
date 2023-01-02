@extends('admin.base.index')
@section('contents')
  <doc-mng-category-component
    :pproutes="{ 
      index: '{{ route('admin.document_mng.category.index') }}', 
      dataList: '{{ route('admin.document_mng.category.dataList') }}', 
      getCategory: '{{ route('admin.document_mng.category.getCategory') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </doc-mng-category-component>
@endsection