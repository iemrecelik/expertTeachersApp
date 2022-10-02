@extends('admin.base.index')
@section('contents')
  <teacher-infos-component
    :ppteacher="{{ json_encode($teacher) }}"
  >
  </teacher-infos-component>
@endsection