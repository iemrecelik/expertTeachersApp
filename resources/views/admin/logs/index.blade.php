@extends('admin.base.index')
@section('contents')
  <logs-component
    :pproutes="{ 
      index: '{{ route('admin.logs.index') }}', 
      getLogsList: '{{ route('admin.logs.getLogsList') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppdatas="{{ json_encode($datas) }}"
  >
  </logs-component>
@endsection