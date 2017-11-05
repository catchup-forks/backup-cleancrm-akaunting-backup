@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.vendors', 1)]))

@section('content')
  <!-- Default box -->
<div class="box box-success">
  {!! Form::open(['url' => 'expenses/vendors', 'role' => 'form']) !!}

  <div class="box-body">
    {{ Form::textGroup('name', trans('general.name'), 'id-card-o') }}

    {{ Form::textGroup('email', trans('general.email'), 'envelope') }}

    {{ Form::textGroup('tax_number', trans('general.tax_number'), 'percent', []) }}

    {{ Form::selectGroup('currency_code', trans_choice('general.currencies', 1), 'exchange', $currencies, setting('general.default_currency')) }}

    {{ Form::textGroup('phone', trans('general.phone'), 'phone', []) }}

    {{ Form::textGroup('website', trans('general.website'), 'globe',[]) }}

    {{ Form::textareaGroup('address', trans('general.address')) }}

    {{ Form::radioGroup('enabled', trans('general.enabled')) }}
  </div>
  <!-- /.box-body -->

  <div class="box-footer">
    {{ Form::saveButtons('expenses/vendors') }}
  </div>
  <!-- /.box-footer -->

  {!! Form::close() !!}
</div>
@endsection

@section('scripts')
  <script type="text/javascript">
    var text_yes = '{{ trans('general.yes') }}';
    var text_no = '{{ trans('general.no') }}';

    $(document).ready(function () {
      $('#enabled_1').trigger('click');

      $('#name').focus();

      $("#currency_code").select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
      });
    });
  </script>
@endsection
