@extends('admin.base.index')
@section('contents')

{{-- {{ json_encode(Auth::user()) }} --}}

  <teachers-component
    :pproutes="{ 
      index: '{{ route('admin.teachers.index') }}', 
      addExcel: '{{ route('admin.teachers.addExcel') }}', 
      addExcelValidation: '{{ route('admin.teachers.addExcelValidation') }}', 
      storeImages: '{{ route('admin.teachers.store.images') }}', 
      dataList: '{{ route('admin.teachers.dataList') }}', 
      getInstitutions: '{{ route('admin.institutions.getInstitutions') }}', 
      showTeacherInfos: '{{ route('admin.teachers.infos') }}',
      getProvincesList: '{{ route('admin.teachers.getProvincesList') }}',
      getTownsList: '{{ route('admin.teachers.getTownsList') }}',
      exportExcelDatas: '{{ route('admin.teachers.exportExcelDatas') }}',
      storeWithMebbis: '{{ route('admin.teachers.store.withMebbis') }}'
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppdatas="{{ empty(session('datas')) ? json_encode($datas) : json_encode(session('datas')) }}"
    :ppauthuser="{{ json_encode(Auth::user()) }}"
  >
  </teachers-component>
@endsection