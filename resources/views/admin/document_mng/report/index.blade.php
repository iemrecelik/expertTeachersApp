@extends('admin.base.index')
@section('contents')
  <doc-mng-report-component
    :pproutes="{ 
      index: '{{ route('admin.document_mng.report.index') }}', 
      saveDocumentRecordCount: '{{ route('admin.document_mng.report.saveDocumentRecordCount') }}', 
    }"
    :pperrors="{{ count($errors) > 0?$errors:'{}' }}"
    :ppdatas="{{ json_encode($datas) }}"
  >
  </doc-mng-report-component>
@endsection