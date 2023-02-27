@extends('admin.base.index')
@section('contents')
  <search-component
    :ppdatas="{ 
      getSearchExpertInfoListSrc: '{{ route("admin.old_regulation.searchList") }}',
    }"
  >
  </search-component>
@endsection