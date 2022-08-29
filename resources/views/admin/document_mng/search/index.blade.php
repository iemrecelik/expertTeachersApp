@extends('admin.base.index')
@section('contents')
  <doc-mng-search-component
    :pproutes="{
      getSearchDocuments: '{{ route("admin.document_mng.search.getSearchDocuments") }}', 
      show: '/admin/document-management/search', 
      getCategory: '{{ route('admin.document_mng.category.getCategory') }}', 
    }"
  >
  </doc-mng-search-component>
@endsection