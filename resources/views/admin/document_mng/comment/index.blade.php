@extends('admin.base.index')
@section('contents')
  <doc-mng-comment-component
    :pproutes="{ 
      index: '{{ route('admin.document_mng.comment.index') }}', 
      dataList: '{{ route('admin.document_mng.comment.dataList') }}', 
      getComments: '{{ route('admin.document_mng.comment.getComments') }}', 
      dcShow: '/admin/document-management/search', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppusers="{{ json_encode($datas) }}"
  >
  </doc-mng-comment-component>
@endsection