@extends('admin.base.index')
@section('contents')
  <my-settings-component
    :pproutes="{ 
      index: '{{ route('admin.mySettings.index') }}', 
      update: '{{ route('admin.mySettings.update') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsuccess="'{{ session('succeed') ?? '' }}'"
    :ppuser="{{ json_encode($user) }}"
  >
  </my-settings-component>
@endsection