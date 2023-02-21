@extends('admin.base.index')
@section('contents')
  <doc-mng-waiting-component
    :pproutes="{ 
      index: '{{ route('admin.document_mng.waiting.index') }}', 
      getWaitingDocument: '{{ route('admin.document_mng.waiting.getWaitingDocument') }}',
      saveBotDocument: '{{ route('admin.document_mng.waiting.saveBotDocument') }}',
      getList: '{{ route('admin.document_mng.list.getList') }}', 
      getListAndSelected: '{{ route('admin.document_mng.list.getListAndSelected') }}', 
      addList: '{{ route('admin.document_mng.list.addList') }}',
      deleteList: '{{ route('admin.document_mng.list.deleteList') }}',
      getComments: '{{ route('admin.document_mng.comment.getComments') }}',
      addComment: '{{ route('admin.document_mng.comment.addComment') }}',
      deleteComment: '{{ route('admin.document_mng.comment.deleteComment') }}',
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsuccess="'{{ session('succeed') ?? '' }}'"
  >
  </doc-mng-waiting-component>
@endsection