@extends('admin.base.index')
@section('contents')
  <lawsuit-mng-lawsuits-component
    :pproutes="{ 
      index: '{{ route('admin.lawsuit_mng.lawsuits.index') }}', 
      dataList: '{{ route('admin.lawsuit_mng.lawsuits.dataList') }}', 
      getTeachersSearchList: '{{ route('admin.teachers.searchList') }}',
      getUnionsSearchList: '{{ route('admin.unions.searchList') }}',
      getDocumentSearchList: '{{ route('admin.document_mng.document.searchList') }}',
      getLawBriefSearchList: '{{ route('admin.lawsuit_mng.lawsuits.searchList') }}',
      lawInfos: '{{ route('admin.lawsuit_mng.lawsuits.lawInfos') }}',
      getUnions: '{{ route('admin.unions.getUnions') }}',
      searchLawsuit: '{{ route('admin.lawsuit_mng.lawsuits.searchLawsuitList') }}',
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </lawsuit-mng-lawsuits-component>
@endsection