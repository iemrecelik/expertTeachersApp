@extends('admin.base.index')
@section('contents')
  <user-component
    :pproutes="{ 
      index: '{{ route('admin.user.index') }}', 
      dataList: '{{ route('admin.user.dataList') }}',
      getPermission: '{{ route('admin.permission.getPermission') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </user-component>
@endsection