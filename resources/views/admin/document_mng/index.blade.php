@extends('admin.base.index')
@section('contents')
  <doc-mng-document-component
    :pproutes="{}"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
  >
  </doc-mng-document-component>
@endsection