<?php
namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\Currency as Request;
use App\Models\Banking\Account;
use App\Models\Setting\Currency;
use ClickNow\Money\Currency as MoneyCurrency;

class Currencies extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $currencies = Currency::collect();
        return view('settings.currencies.index', compact('currencies', 'codes', 'rates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // Prepare codes
        $codes = array();
        $currencies = MoneyCurrency::getCurrencies();
        foreach ($currencies as $key => $item) {
            $codes[$key] = $key;
        }
        return view('settings.currencies.create', compact('codes'));
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
        Currency::create($request->all());
        // Update default currency setting
        if ($request['default_currency']) {
            setting()->set('general.default_currency', $request['code']);
            setting()->save();
        }
        $message = trans('messages.success.added', ['type' => trans_choice('general.currencies', 1)]);
        flash($message)->success();
        return redirect('settings/currencies');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Currency $currency
     *
     * @return Response
     */
    public function edit(Currency $currency)
    {
        // Prepare codes
        $codes = array();
        $currencies = MoneyCurrency::getCurrencies();
        foreach ($currencies as $key => $item) {
            $codes[$key] = $key;
        }
        // Set default currency
        $currency->default_currency = ($currency->code == setting('general.default_currency')) ? 1 : 0;
        return view('settings.currencies.edit', compact('currency', 'codes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Currency $currency
     * @param  Request $request
     *
     * @return Response
     */
    public function update(Currency $currency, Request $request)
    {
        $relationships = $this->countRelationships($currency, [
          'bankaccounts' => 'bankaccounts',
          'customers' => 'customers',
          'invoices' => 'invoices',
          'revenues' => 'revenues',
          'bills' => 'bills',
          'payments' => 'payments',
        ]);
        if ($currency->code == setting('general.default_currency')) {
            $relationships[] = strtolower(trans_choice('general.companies', 1));
        }
        if (empty($relationships) || $request['enabled']) {
            $currency->update($request->all());
            // Update default currency setting
            if ($request['default_currency']) {
                setting()->set('general.default_currency', $request['code']);
                setting()->save();
            }
            $message = trans('messages.success.updated', ['type' => trans_choice('general.currencies', 1)]);
            flash($message)->success();
            return redirect('settings/currencies');
        } else {
            $message = trans('messages.warning.disabled',
              ['name' => $currency->name, 'text' => implode(', ', $relationships)]);
            flash($message)->warning();
            return redirect('settings/currencies/' . $currency->id . '/edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Currency $currency
     *
     * @return Response
     */
    public function destroy(Currency $currency)
    {
        $relationships = $this->countRelationships($currency, [
          'bankaccounts' => 'bankaccounts',
          'customers' => 'customers',
          'invoices' => 'invoices',
          'revenues' => 'revenues',
          'bills' => 'bills',
          'payments' => 'payments',
        ]);
        if ($currency->code == setting('general.default_currency')) {
            $relationships[] = strtolower(trans_choice('general.companies', 1));
        }
        if (empty($relationships)) {
            $currency->delete();
            $message = trans('messages.success.deleted', ['type' => trans_choice('general.currencies', 1)]);
            flash($message)->success();
        } else {
            $message = trans('messages.warning.deleted',
              ['name' => $currency->name, 'text' => implode(', ', $relationships)]);
            flash($message)->warning();
        }
        return redirect('settings/currencies');
    }

    public function currency()
    {
        $json = new \stdClass();
        $account_id = request('bankaccount_id');
        if ($account_id) {
            $currencies = Currency::enabled()->pluck('name', 'code')->toArray();
            $json->currency_code = Account::where('id', $account_id)->pluck('currency_code')->first();
            $json->currency_name = $currencies[$json->currency_code];
        }
        return response()->json($json);
    }
}
