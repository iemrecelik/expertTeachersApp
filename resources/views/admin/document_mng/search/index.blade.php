@extends('admin.base.index')
@section('contents')
  <doc-mng-search-component
    :pproutes="{
      getSearchDocuments: '{{ route("admin.document_mng.search.getSearchDocuments") }}', 
      show: '/admin/document-management/search', 
      getCategoryAndList: '{{ route('admin.document_mng.search.getCategoryAndList') }}', 
      getCategory: '{{ route('admin.document_mng.category.getCategory') }}', 
      getList: '{{ route('admin.document_mng.list.getList') }}', 
      getListAndSelected: '{{ route('admin.document_mng.list.getListAndSelected') }}', 
      addList: '{{ route('admin.document_mng.list.addList') }}',
      addComment: '{{ route('admin.document_mng.comment.addComment') }}',
      deleteList: '{{ route('admin.document_mng.list.deleteList') }}',
    }"
  >
  </doc-mng-search-component>
@endsection