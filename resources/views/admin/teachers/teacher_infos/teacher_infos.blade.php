@extends('admin.base.index')
@section('contents')
  <teacher-infos-component
    :ppteacher="{{ $teacher ? json_encode($teacher) : '{}' }}"
    :pptcvalid="{{ json_encode($tcValid) }}"
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
      addLawFile: '{{ route('admin.teachers.teacherInfos.addLawFile') }}',
      getDocumentSearchList: '{{ route('admin.document_mng.document.searchList') }}',
      addDocumentToTeacher: '{{ route('admin.teachers.infos.addDocumentToTeacher') }}',
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppoldinput="'{{ json_encode(session()->getOldInput()) }}'"
  >
  </teacher-infos-component>
@endsection