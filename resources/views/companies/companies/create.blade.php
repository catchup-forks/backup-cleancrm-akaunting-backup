@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.companies', 1)]))

@section('content')
  <!-- Default box -->
<div class="box box-success">
  {!! Form::open(['url' => 'companies/companies', 'files' => true, 'role' => 'form']) !!}
  <div class="box-body">
    {{ Form::textGroup('company_name', trans('general.name'), 'id-card-o') }}

    {{ Form::textGroup('domain', trans('companies.domain'), 'globe') }}

    {{ Form::emailGroup('company_email', trans('general.email'), 'envelope') }}

    {{ Form::selectGroup('default_currency', trans_choice('general.currencies', 1), 'money', $currencies) }}

    {{ Form::textareaGroup('company_address', trans('general.address')) }}

    {{ Form::fileGroup('company_logo', trans('companies.logo')) }}

    {{ Form::radioGroup('enabled', trans('general.enabled')) }}
  </div>
  <!-- /.box-body -->

  <div class="box-footer">
    {{ Form::saveButtons('companies/companies') }}
  </div>
  <!-- /.box-footer -->

  {!! Form::close() !!}
</div>
@endsection

@section('js')
  <script src="{{ asset('js/bootstrap-fancyfile.js') }}"></script>
@endsection

@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fancyfile.css') }}">
@endsection

@section('scripts')
  <script type="text/javascript">
    var text_yes = '{{ trans('general.yes') }}';
    var text_no = '{{ trans('general.no') }}';

    $(document).ready(function () {
      $("#default_currency").select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
      });

      $('#company_logo').fancyfile({
        text: '{{ trans('general.form.select.file') }}',
        style: 'btn-default',
        placeholder: '{{ trans('general.form.no_file_selected') }}'
      });
    });
  </script>
@endsection
