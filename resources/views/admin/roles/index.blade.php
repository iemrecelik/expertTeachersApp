@extends('admin.base.index')
@section('contents')
  <roles-component
    :pproutes="{ 
      index: '{{ route('admin.roles.index') }}', 
      dataList: '{{ route('admin.roles.dataList') }}',
      getPermission: '{{ route('admin.permission.getPermission') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </roles-component>
@endsection