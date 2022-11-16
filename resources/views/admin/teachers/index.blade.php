@extends('admin.base.index')
@section('contents')
  <teachers-component
    :pproutes="{ 
      index: '{{ route('admin.teachers.index') }}', 
      addExcel: '{{ route('admin.teachers.addExcel') }}', 
      dataList: '{{ route('admin.teachers.dataList') }}', 
      getInstitutions: '{{ route('admin.institutions.getInstitutions') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppdatas="{{ json_encode($datas) }}"
  >
  </teachers-component>
@endsection