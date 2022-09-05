@extends('admin.base.index')
@section('contents')
  <doc-mng-list-component
    :pproutes="{ 
      index: '{{ route('admin.document_mng.list.index') }}', 
      dataList: '{{ route('admin.document_mng.list.dataList') }}', 
      getList: '{{ route('admin.document_mng.list.getList') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppusers="{{ json_encode($datas) }}"
  >
  </doc-mng-list-component>
@endsection