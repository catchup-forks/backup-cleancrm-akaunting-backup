@extends('layouts.invoice')

@section('title', trans_choice('general.invoices', 1) . ': ' . $invoice->invoice_number)

@section('content')
  <section class="invoice">
    <div class="row invoice-header">
      <div class="col-xs-7">
        @if (setting('general.invoice_logo'))
          <img src="{{ asset(setting('general.invoice_logo')) }}" class="invoice-logo"/>
        @else
          <img src="{{ asset(setting('general.company_logo')) }}" class="invoice-logo"/>
        @endif
      </div>
      <div class="col-xs-5 invoice-company">
        <address>
          <strong>{{ setting('general.company_name') }}</strong><br>
          {{ setting('general.company_address') }}<br>
          @if (setting('general.company_tax_number'))
            {{ trans('general.tax_number') }}: {{ setting('general.company_tax_number') }}<br>
          @endif
          <br>
          @if (setting('general.company_phone'))
            {{ setting('general.company_phone') }}<br>
          @endif
          {{ setting('general.company_email') }}
        </address>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-7">
        {{ trans('invoices.bill_to') }}
        <address>
          <strong>{{ $invoice->customer_name }}</strong><br>
          {{ $invoice->customer_address }}<br>
          @if ($invoice->customer_tax_number)
            {{ trans('general.tax_number') }}: {{ $invoice->customer_tax_number }}<br>
          @endif
          <br>
          @if ($invoice->customer_phone)
            {{ $invoice->customer_phone }}<br>
          @endif
          {{ $invoice->customer_email }}
        </address>
      </div>
      <div class="col-xs-5">
        <div class="table-responsive">
          <table class="table no-border">
            <tbody>
            <tr>
              <th>{{ trans('invoices.invoice_number') }}:</th>
              <td class="text-right">{{ $invoice->invoice_number }}</td>
            </tr>
            @if ($invoice->order_number)
              <tr>
                <th>{{ trans('invoices.order_number') }}:</th>
                <td class="text-right">{{ $invoice->order_number }}</td>
              </tr>
            @endif
            <tr>
              <th>{{ trans('invoices.invoice_date') }}:</th>
              <td class="text-right">{{ Date::parse($invoice->invoiced_at)->format($date_format) }}</td>
            </tr>
            <tr>
              <th>{{ trans('invoices.payment_due') }}:</th>
              <td class="text-right">{{ Date::parse($invoice->due_at)->format($date_format) }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
          <tbody>
          <tr>
            <th>{{ trans_choice('general.items', 1) }}</th>
            <th class="text-center">{{ trans('invoices.quantity') }}</th>
            <th class="text-right">{{ trans('invoices.price') }}</th>
            <th class="text-right">{{ trans('invoices.total') }}</th>
          </tr>
          @foreach($invoice->items as $item)
            <tr>
              <td>
                {{ $item->name }}
                @if ($item->sku)
                  <br>
                  <small>{{ trans('items.sku') }}: {{ $item->sku }}</small>
                @endif
              </td>
              <td class="text-center">{{ $item->quantity }}</td>
              <td class="text-right">@money($item->price, $invoice->currency_code, true)</td>
              <td class="text-right">@money($item->total - $item->tax, $invoice->currency_code, true)</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-7">
        @if ($invoice->notes)
          <p class="lead">{{ trans_choice('general.notes', 2) }}</p>

          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            {{ $invoice->notes }}
          </p>
        @endif
      </div>
      <div class="col-xs-5">
        <div class="table-responsive">
          <table class="table">
            <tbody>
            <tr>
              <th style="max-width: 214px">{{ trans('invoices.sub_total') }}:</th>
              <td class="text-right">@money($invoice->sub_total, $invoice->currency_code, true)</td>
            </tr>
            <tr>
              <th>{{ trans('invoices.tax_total') }}:</th>
              <td class="text-right">@money($invoice->tax_total, $invoice->currency_code, true)</td>
            </tr>
            @if($invoice->paid)
              <tr>
                <th>{{ trans('invoices.paid') }}:</th>
                <td class="text-right">@money('-' . $invoice->paid, $invoice->currency_code, true)</td>
              </tr>
            @endif
            <tr>
              <th>{{ trans('invoices.total') }}:</th>
              <td class="text-right">@money($invoice->grand_total, $invoice->currency_code, true)</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection
