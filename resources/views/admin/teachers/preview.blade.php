@extends('admin.base.index')
@section('contents')
  <teachers-preview-component
    :pproutes="{ 
      index: '{{ route('admin.teachers.index') }}', 
      addExcel: '{{ route('admin.teachers.addExcel') }}', 
      dataList: '{{ route('admin.teachers.dataList') }}', 
      getInstitutions: '{{ route('admin.institutions.getInstitutions') }}', 
      preview: '{{ route('admin.teachers.preview') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :pptbodyHtml="{{ json_encode($tbodyHtml) }}"
  >
  </teachers-preview-component>
@endsection