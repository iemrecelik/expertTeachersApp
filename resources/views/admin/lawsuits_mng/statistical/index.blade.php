@extends('admin.base.index')
@section('contents')
  <lawsuit-mng-statistical-component
    :pproutes="{ 
      index: '{{ route('admin.lawsuit_mng.statistical.index') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </lawsuit-mng-statistical-component>
@endsection