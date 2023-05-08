@extends('admin.base.index')
@section('contents')
  <archvie-component
    :pproutes="{ 
      index: '{{ route('admin.archive.index') }}',
      recordArchive: '{{ route('admin.archive.record') }}',
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppdatas="{{ json_encode($datas) }}"
  >
  </archvie-component>
@endsection