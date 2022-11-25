@extends('admin.base.index')
@section('contents')
  <teachers-component
    :pproutes="{ 
      index: '{{ route('admin.teachers.index') }}', 
      addExcel: '{{ route('admin.teachers.addExcel') }}', 
      addExcelValidation: '{{ route('admin.teachers.addExcelValidation') }}', 
      storeImages: '{{ route('admin.teachers.store.images') }}', 
      dataList: '{{ route('admin.teachers.dataList') }}', 
      getInstitutions: '{{ route('admin.institutions.getInstitutions') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppdatas="{{ empty(session('datas')) ? json_encode($datas) : json_encode(session('datas')) }}"
  >
  </teachers-component>
@endsection