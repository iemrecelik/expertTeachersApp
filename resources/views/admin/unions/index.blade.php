@extends('admin.base.index')
@section('contents')
  <unions-mng-component
    :pproutes="{ 
      index: '{{ route('admin.unions.index') }}', 
      dataList: '{{ route('admin.unions.dataList') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </unions-mng-component>
@endsection