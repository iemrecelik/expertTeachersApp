@extends('admin.base.index')
@section('contents')
  <settings-component
    :pproutes="{ 
      index: '{{ route('admin.settings.index') }}', 
      update: '{{ route('admin.settings.update') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppsuccess="'{{ session('succeed') ?? '' }}'"
    :ppipnames="{{ json_encode($ipNames) }}"
    :ppsignaturenames="{{ json_encode($signatureNames) }}"
  >
  </settings-component>
@endsection