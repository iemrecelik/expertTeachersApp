@extends('admin.base.index')
@section('contents')
  <teachers-preview-component
    :pproutes="{ 
      index: '{{ route('admin.teachers.index') }}', 
      addExcel: '{{ route('admin.teachers.addExcel') }}', 
      dataList: '{{ route('admin.teachers.dataList') }}', 
      getInstitutions: '{{ route('admin.institutions.getInstitutions') }}', 
      storeExcel: '{{ route('admin.teachers.store.excel') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsucceed="{{ empty($succeed) ? '{}': $succeed }}"
    :ppdatas="{{ json_encode($datas) }}"
  >
  </teachers-preview-component>
@endsection