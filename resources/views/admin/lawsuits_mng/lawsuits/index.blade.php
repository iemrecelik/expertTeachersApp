@extends('admin.base.index')
@section('contents')
  <lawsuit-mng-lawsuits-component
    :pproutes="{ 
      index: '{{ route('admin.lawsuit_mng.lawsuits.index') }}', 
      dataList: '{{ route('admin.lawsuit_mng.lawsuits.dataList') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </lawsuit-mng-lawsuits-component>
@endsection