@extends('admin.base.index')
@section('contents')
  <lawsuit-mng-statistical-component
    :pproutes="{ 
      index: '{{ route('admin.lawsuit_mng.statistical.index') }}', 
      statsToPdf: '{{ route('admin.lawsuit_mng.statistical.statsToPdf') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppstats="{{ json_encode($stats) }}"
  >
  </lawsuit-mng-statistical-component>
@endsection