@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.invoices', 1)]))

@section('content')
  <!-- Default box -->
<div class="box box-success">
  {!! Form::open(['url' => 'incomes/invoices', 'files' => true, 'role' => 'form']) !!}

  <div class="box-body">
    {{ Form::selectGroup('customer_id', trans_choice('general.customers', 1), 'user', $customers) }}

    {{ Form::selectGroup('currency_code', trans_choice('general.currencies', 1), 'exchange', $currencies, setting('general.default_currency')) }}

    {{ Form::textGroup('invoiced_at', trans('invoices.invoice_date'), 'calendar',['id' => 'invoiced_at', 'class' => 'form-control', 'required' => 'required', 'data-inputmask' => '\'alias\': \'yyyy/mm/dd\'', 'data-mask' => ''], Date::now()->toDateString()) }}

    {{ Form::textGroup('due_at', trans('invoices.due_date'), 'calendar',['id' => 'due_at', 'class' => 'form-control', 'required' => 'required', 'data-inputmask' => '\'alias\': \'yyyy/mm/dd\'', 'data-mask' => '']) }}

    {{ Form::textGroup('invoice_number', trans('invoices.invoice_number'), 'file-text-o', ['required' => 'required'], $number) }}

    {{ Form::textGroup('order_number', trans('invoices.order_number'), 'shopping-cart', []) }}

    <div class="form-group col-md-12">
      {!! Form::label('items', 'Items', ['class' => 'control-label']) !!}
      <div class="table-responsive">
        <table class="table table-bordered" id="items">
          <thead>
          <tr style="background-color: #f9f9f9;">
            <th width="5%" class="text-center">{{ trans('general.actions') }}</th>
            <th width="40%" class="text-left">{{ trans('general.name') }}</th>
            <th width="5%" class="text-center">{{ trans('invoices.quantity') }}</th>
            <th width="10%" class="text-right">{{ trans('invoices.price') }}</th>
            <th width="15%" class="text-right">{{ trans_choice('general.taxes', 1) }}</th>
            <th width="10%" class="text-right">{{ trans('invoices.total') }}</th>
          </tr>
          </thead>
          <tbody>
          <?php $item_row = 0; ?>
          <tr id="item-row-{{ $item_row }}">
            <td class="text-center" style="vertical-align: middle;">
              <button type="button"
                      onclick="$(this).tooltip('destroy'); $('#item-row-{{ $item_row }}').remove(); totalItem();"
                      data-toggle="tooltip" title="{{ trans('general.delete') }}" class="btn btn-xs btn-danger"><i
                  class="fa fa-trash"></i></button>
            </td>
            <td>
              <input class="form-control typeahead" required="required"
                     placeholder="{{ trans('general.form.enter', ['field' => trans_choice('invoices.item_name', 1)]) }}"
                     name="item[{{ $item_row }}][name]" type="text" id="item-name-{{ $item_row }}">
              <input name="item[{{ $item_row }}][item_id]" type="hidden" id="item-id-{{ $item_row }}">
            </td>
            <td>
              <input class="form-control text-center" required="required" name="item[{{ $item_row }}][quantity]"
                     type="text" id="item-quantity-{{ $item_row }}">
            </td>
            <td>
              <input class="form-control text-right" required="required" name="item[{{ $item_row }}][price]" type="text"
                     id="item-price-{{ $item_row }}">
            </td>
            <td>
              {!! Form::select('item[' . $item_row . '][tax_id]', $taxes, setting('general.default_tax'), ['id'=> 'item-tax-'. $item_row, 'class' => 'form-control select2', 'placeholder' => trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)])]) !!}
            </td>
            <td class="text-right" style="vertical-align: middle;">
              <span id="item-total-{{ $item_row }}">0</span>
            </td>
          </tr>
          <?php $item_row++; ?>
          <tr id="addItem">
            <td class="text-center">
              <button type="button" onclick="addItem();" data-toggle="tooltip" title="{{ trans('general.add') }}"
                      class="btn btn-xs btn-primary" data-original-title="{{ trans('general.add') }}"><i
                  class="fa fa-plus"></i></button>
            </td>
            <td class="text-right" colspan="5"></td>
          </tr>
          <tr>
            <td class="text-right" colspan="5"><strong>{{ trans('invoices.sub_total') }}</strong></td>
            <td class="text-right"><span id="sub-total">0</span></td>
          </tr>
          <tr>
            <td class="text-right" colspan="5"><strong>{{ trans_choice('general.taxes', 1) }}</strong></td>
            <td class="text-right"><span id="tax-total">0</span></td>
          </tr>
          <tr>
            <td class="text-right" colspan="5"><strong>{{ trans('invoices.total') }}</strong></td>
            <td class="text-right"><span id="grand-total">0</span></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    {{ Form::textareaGroup('notes', trans_choice('general.notes', 2)) }}

    {{ Form::fileGroup('attachment', trans('general.attachment')) }}
  </div>
  <!-- /.box-body -->

  <div class="box-footer">
    {{ Form::saveButtons('incomes/invoices') }}
  </div>
  <!-- /.box-footer -->

  {!! Form::close() !!}
</div>
@endsection

@section('js')
  <script src="{{ asset('/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
  <script src="{{ asset('js/bootstrap-fancyfile.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
@endsection

@section('css')
  <link rel="stylesheet" href="{{ asset('/plugins/datepicker/datepicker3.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fancyfile.css') }}">
@endsection

@section('scripts')
  <script type="text/javascript">
    var item_row = '{{ $item_row }}';

    function addItem() {
      html = '<tr id="item-row-' + item_row + '">';
      html += '  <td class="text-center" style="vertical-align: middle;">';
      html += '      <button type="button" onclick="$(this).tooltip(\'destroy\'); $(\'#item-row-' + item_row + '\').remove(); totalItem();" data-toggle="tooltip" title="{{ trans('general.delete') }}" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>';
      html += '  </td>';
      html += '  <td>';
      html += '      <input class="form-control typeahead" required="required" placeholder="{{ trans('general.form.enter', ['field' => trans_choice('invoices.item_name', 1)]) }}" name="item[' + item_row + '][name]" type="text" id="item-name-' + item_row + '">';
      html += '      <input name="item[' + item_row + '][item_id]" type="hidden" id="item-id-' + item_row + '">';
      html += '  </td>';
      html += '  <td>';
      html += '      <input class="form-control text-center" required="required" name="item[' + item_row + '][quantity]" type="text" id="item-quantity-' + item_row + '">';
      html += '  </td>';
      html += '  <td>';
      html += '      <input class="form-control text-right" required="required" name="item[' + item_row + '][price]" type="text" id="item-price-' + item_row + '">';
      html += '  </td>';
      html += '  <td>';
      html += '      <select class="form-control select2" name="item[' + item_row + '][tax_id]" id="item-tax-' + item_row + '">';
      html += '         <option selected="selected" value="">{{ trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)]) }}</option>';
      @foreach($taxes as $tax_key => $tax_value)
        html += '         <option value="{{ $tax_key }}">{{ $tax_value }}</option>';
      @endforeach
        html += '      </select>';
      html += '  </td>';
      html += '  <td class="text-right" style="vertical-align: middle;">';
      html += '      <span id="item-total-' + item_row + '">0</span>';
      html += '  </td>';

      $('#items tbody #addItem').before(html);
      //$('[rel=tooltip]').tooltip();

      $('[data-toggle="tooltip"]').tooltip('hide');

      $('#item-row-' + item_row + ' .select2').select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)]) }}"
      });

      item_row++;
    }

    $(document).ready(function () {
      //Date picker
      $('#invoiced_at').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
      });

      //Date picker
      $('#due_at').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
      });

      $(".select2").select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.taxes', 1)]) }}"
      });

      $("#customer_id").select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.customers', 1)]) }}"
      });

      $("#currency_code").select2({
        placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
      });

      $('#attachment').fancyfile({
        text: '{{ trans('general.form.select.file') }}',
        style: 'btn-default',
        placeholder: '{{ trans('general.form.no_file_selected') }}'
      });

      var autocomplete_path = "{{ url('items/items/autocomplete') }}";

      $(document).on('click', '.form-control.typeahead', function () {
        input_id = $(this).attr('id').split('-');

        item_id = parseInt(input_id[input_id.length - 1]);

        $(this).typeahead({
          minLength: 3,
          displayText: function (data) {
            return data.name;
          },
          source: function (query, process) {
            $.ajax({
              url: autocomplete_path,
              type: 'GET',
              dataType: 'JSON',
              data: 'query=' + query + '&type=invoice&currency_code=' + $('#currency_code').val(),
              success: function (data) {
                return process(data);
              }
            });
          },
          afterSelect: function (data) {
            $('#item-id-' + item_id).val(data.item_id);
            $('#item-quantity-' + item_id).val('1');
            $('#item-price-' + item_id).val(data.sale_price);
            $('#item-tax-' + item_id).val(data.tax_id);

            // This event Select2 Stylesheet
            $('#item-tax-' + item_id).trigger('change');

            $('#item-total-' + item_id).html(data.total);

            totalItem();
          }
        });
      });

      $(document).on('change', '#currency_code, #items tbody select', function () {
        totalItem();
      });

      $(document).on('keyup', '#items tbody .form-control', function () {
        totalItem();
      });

      $(document).on('change', '#customer_id', function (e) {
        $.ajax({
          url: '{{ url("incomes/customers/currency") }}',
          type: 'GET',
          dataType: 'JSON',
          data: 'customer_id=' + $(this).val(),
          success: function (data) {
            $('#currency_code').val(data.currency_code);

            // This event Select2 Stylesheet
            $('#currency_code').trigger('change');
          }
        });
      });
    });

    function totalItem() {
      $.ajax({
        url: '{{ url("items/items/totalItem") }}',
        type: 'POST',
        dataType: 'JSON',
        data: $('#currency_code, #items input[type=\'text\'],#items input[type=\'hidden\'], #items textarea, #items select'),
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        success: function (data) {
          if (data) {
            $.each(data.items, function (key, value) {
              $('#item-total-' + key).html(value);
            });

            $('#sub-total').html(data.sub_total);
            $('#tax-total').html(data.tax_total);
            $('#grand-total').html(data.grand_total);
          }
        }
      });
    }
  </script>
@endsection
