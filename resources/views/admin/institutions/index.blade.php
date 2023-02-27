@extends('admin.base.index')
@section('contents')
  <teach-mng-institutions-component
    :pproutes="{ 
      index: '{{ route('admin.institutions.index') }}', 
      dataList: '{{ route('admin.institutions.dataList') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </teach-mng-institutions-component>
@endsection