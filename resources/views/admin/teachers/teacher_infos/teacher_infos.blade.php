@extends('admin.base.index')
@section('contents')
  <teacher-infos-component
    :ppteacher="{{ json_encode($teacher) }}"
    :pproutes="{
      getSearchDocuments: '{{ route("admin.document_mng.search.getSearchDocuments") }}', 
      show: '/admin/document-management/search', 
      getList: '{{ route('admin.document_mng.list.getList') }}', 
      getListAndSelected: '{{ route('admin.document_mng.list.getListAndSelected') }}', 
      addList: '{{ route('admin.document_mng.list.addList') }}',
      deleteList: '{{ route('admin.document_mng.list.deleteList') }}',
      getComments: '{{ route('admin.document_mng.comment.getComments') }}',
      addComment: '{{ route('admin.document_mng.comment.addComment') }}',
      deleteComment: '{{ route('admin.document_mng.comment.deleteComment') }}',
    }"
  >
  </teacher-infos-component>
@endsection