<?php
namespace App\Http\Controllers\Banking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banking\Transfer as Request;
use App\Models\Banking\Account;
use App\Models\Banking\Transfer;
use App\Models\Expense\Payment;
use App\Models\Income\Revenue;
use App\Models\Setting\Category;
use App\Models\Setting\Currency;
use App\Utilities\Modules;

class Transfers extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $request = request();
        $items = Transfer::with(['payment', 'revenue', 'account'])->collect('payment.paid_at');
        $bankaccounts = collect(Account::enabled()->pluck('name', 'id'))
          ->prepend(trans('general.all_type', ['type' => trans_choice('general.bankaccounts', 2)]), '');
        $transfers = array();
        foreach ($items as $item) {
            $payment = $item->payment;
            $revenue = $item->revenue;
            $transfers[] = (object)[
              'from_account' => $payment->bankaccount->name,
              'to_account' => $revenue->bankaccount->name,
              'amount' => $payment->amount,
              'currency_code' => $payment->currency_code,
              'paid_at' => $payment->paid_at,
            ];
        }
        $special_key = array(
          'payment.name' => 'from_account',
          'revenue.name' => 'to_account',
        );
        if (isset($request['sort']) && array_key_exists($request['sort'], $special_key)) {
            $sort_order = array();
            foreach ($transfers as $key => $value) {
                $sort = $request['sort'];
                if (array_key_exists($request['sort'], $special_key)) {
                    $sort = $special_key[$request['sort']];
                }
                $sort_order[$key] = $value->{$sort};
            }
            $sort_type = (isset($request['order']) && $request['order'] == 'asc') ? SORT_ASC : SORT_DESC;
            array_multisort($sort_order, $sort_type, $transfers);
        }
        return view('banking.transfers.index', compact('transfers', 'items', 'bankaccounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $bankaccounts = Account::enabled()->pluck('name', 'id');
        $payment_methods = Modules::getPaymentMethods();
        return view('banking.transfers.create', compact('bankaccounts', 'payment_methods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $currencies = Currency::enabled()->pluck('rate', 'code')->toArray();
        $payment_currency_code = Account::where('id', $request['from_account_id'])->pluck('currency_code')->first();
        $revenue_currency_code = Account::where('id', $request['to_account_id'])->pluck('currency_code')->first();
        $request['bankaccount_id'] = $request['from_account_id'];
        $request['paid_at'] = $request['transferred_at'];
        // amount
        $request['currency_code'] = $payment_currency_code;
        $request['currency_rate'] = $currencies[$payment_currency_code];
        $request['vendor_id'] = '0';
        // description
        $request['category_id'] = Category::enabled()->type('other')->pluck('id')->first(); // Transfer Category ID
        // payment_method
        // reference
        $request['attachment'] = '';
        $payment = Payment::create($request->all());
        $transfer = new Transfer();
        $transfer->default_currency_code = $payment_currency_code;
        $transfer->amount = $request['amount'];
        $transfer->currency_code = $revenue_currency_code;
        $transfer->currency_rate = $currencies[$revenue_currency_code];
        $amount = $transfer->getDynamicConvertedAmount();
        $request['bankaccount_id'] = $request['to_account_id'];
        // paid_at
        $request['amount'] = $amount;
        $request['currency_code'] = $revenue_currency_code;
        $request['currency_rate'] = $currencies[$revenue_currency_code];
        $request['customer_id'] = '0';
        // description
        // category_id
        // payment_method
        // reference
        // attachment
        $revenue = Revenue::create($request->all());
        $request['payment_id'] = $payment->id;
        $request['revenue_id'] = $revenue->id;
        Transfer::create($request->all());
        $message = trans('messages.success.added', ['type' => trans_choice('general.transfers', 1)]);
        flash($message)->success();
        return redirect('banking/transfers');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function edit(Request $request)
    {
        $payment = Payment::findOrFail($request['payment_id']);
        $revenue = Revenue::findOrFail($request['revenue_id']);
        $transfer['from_account_id'] = $payment->bankaccount_id;
        $transfer['to_account_id'] = $revenue->bankaccount_id;
        $transfer['transferred_at'] = $revenue->deposited_at;
        $transfer['description'] = $revenue->description;
        $transfer['amount'] = $revenue->amount;
        $transfer['payment_method'] = $revenue->payment_method;
        $transfer['reference'] = $revenue->reference;
        $bankaccounts = Account::listArray();
        $payment_methods = Modules::getPaymentMethods();
        return view('banking.transfers.edit', compact('transfer', 'bankaccounts', 'payment_methods'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Transfer $transfer
     * @param  Request $request
     *
     * @return Response
     */
    public function update(Transfer $transfer, Request $request)
    {
        $currencies = Currency::enabled()->pluck('rate', 'code')->toArray();
        $payment_currency_code = Account::where('id', $request['from_account_id'])->pluck('currency_code')->first();
        $revenue_currency_code = Account::where('id', $request['to_account_id'])->pluck('currency_code')->first();
        $payment = Payment::findOrFail($transfer->payment_id);
        $request['bankaccount_id'] = $request['from_account_id'];
        $request['paid_at'] = $request['transferred_at'];
        // amount
        $request['currency_code'] = $payment_currency_code;
        $request['currency_rate'] = $currencies[$payment_currency_code];
        $request['vendor_id'] = '0';
        // description
        $request['category_id'] = Category::enabled()->type('other')->pluck('id')->first(); // Transfer Category ID
        // payment_method
        // reference
        $request['attachment'] = '';
        $payment->update($request->all());
        $revenue = Revenue::findOrFail($transfer->income_id);
        $transfer = new Transfer();
        $transfer->default_currency_code = $payment_currency_code;
        $transfer->amount = $request['amount'];
        $transfer->currency_code = $revenue_currency_code;
        $transfer->currency_rate = $currencies[$revenue_currency_code];
        $amount = $transfer->getDynamicConvertedAmount();
        $request['bankaccount_id'] = $request['to_account_id'];
        // paid_at
        $request['amount'] = $amount;
        $request['currency_code'] = $revenue_currency_code;
        $request['currency_rate'] = $currencies[$revenue_currency_code];
        $request['customer_id'] = '0';
        // description
        // category_id
        // payment_method
        // reference
        // attachment
        $revenue->update($request->all());
        $request['payment_id'] = $payment->id;
        $request['revenue_id'] = $revenue->id;
        $transfer->update($request->all());
        $message = trans('messages.success.updated', ['type' => trans_choice('general.transfers', 1)]);
        flash($message)->success();
        return redirect('banking/transfers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Transfer $transfer
     *
     * @return Response
     */
    public function destroy(Transfer $transfer)
    {
        $payment = Payment::findOrFail($transfer['payment_id']);
        $revenue = Revenue::findOrFail($transfer['revenue_id']);
        $transfer->delete();
        $payment->delete();
        $revenue->delete();
        $message = trans('messages.success.deleted', ['type' => trans_choice('general.transfers', 1)]);
        flash($message)->success();
        return redirect('banking/transfers');
    }
}
