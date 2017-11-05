@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.transfers', 1)]))

@section('content')
  <!-- Default box -->
<div class="box box-success">
  {!! Form::open(['url' => 'banking/transfers', 'role' => 'form']) !!}

  <div class="box-body">
    {{ Form::selectGroup('from_account_id', trans('transfers.from_account'), 'university', $bankaccounts) }}

    {{ Form::selectGroup('to_account_id', trans('transfers.to_account'), 'university', $bankaccounts) }}

    {{ Form::textGroup('amount', trans('general.amount'), 'money') }}

    {{ Form::textGroup('transferred_at', trans('general.date'), 'calendar',['id' => 'transferred_at', 'required' => 'required', 'data-inputmask' => '\'alias\': \'yyyy-mm-dd\'', 'data-mask' => ''], Date::now()->toDateString()) }}

    {{ Form::textareaGroup('description', trans('general.description')) }}

    {{ Form::selectGroup('payment_method', trans_choice('general.payment_methods', 1), 'credit-card', $payment_methods, setting('general.default_payment_method')) }}

    {{ Form::textGroup('reference', trans('general.reference'), 'file-text-o', []) }}
  </div>
  <!-- /.box-body -->

  <div class="box-footer">
    {{ Form::saveButtons('banking/transfers') }}
  </div>
  <!-- /.box-footer -->

  {!! Form::close() !!}
</div>
@endsection

@section('js')
  <script src="{{ asset('/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
@endsection

@section('css')
  <link rel="stylesheet" href="{{ asset('/plugins/datepicker/datepicker3.css') }}">
@endsection

@section('scripts')
  <script type="text/javascript">
    $(document).ready(function () {
      //Date picker
      $('#transferred_at').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
      });

      $("#from_account_id").select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.bankaccounts', 1)]) }}"
      });

      $("#to_account_id").select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.bankaccounts', 1)]) }}"
      });

      $("#payment_method").select2({
        placeholder: "{{ trans_choice('general.payment_methods', 1) }}"
      });
    });
  </script>
@endsection
