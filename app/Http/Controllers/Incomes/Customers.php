<?php
namespace App\Http\Controllers\Incomes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Income\Customer as Request;
use App\Models\Auth\User;
use App\Models\Income\Customer;
use App\Models\Setting\Currency;

class Customers extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $customers = Customer::collect();
        return view('incomes.customers.index', compact('customers', 'emails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $currencies = Currency::enabled()->pluck('name', 'code');
        return view('incomes.customers.create', compact('currencies'));
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
        $customer = Customer::create($request->all());
        if (!empty($request->input('create_user'))) {
            $user = User::create($request->input());
            $request['staff_id'] = $user->id;
            $request['roles'] = array('3');
            $request['companies'] = array(session('company_id'));
            // Attach roles
            $user->roles()->attach($request['roles']);
            // Attach companies
            $user->companies()->attach($request['companies']);
            $customer->update($request->all());
        }
        $message = trans('messages.success.added', ['type' => trans_choice('general.customers', 1)]);
        flash($message)->success();
        return redirect('incomes/customers');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Customer $customer
     *
     * @return Response
     */
    public function edit(Customer $customer)
    {
        $currencies = Currency::enabled()->pluck('name', 'code');
        return view('incomes.customers.edit', compact('customer', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Customer $customer
     * @param  Request $request
     *
     * @return Response
     */
    public function update(Customer $customer, Request $request)
    {
        $customer->update($request->all());
        if (!empty($request->input('create_user'))) {
            $user = User::create($request->input());
            $request['staff_id'] = $user->id;
            $request['roles'] = array('3');
            $request['companies'] = array(session('company_id'));
            // Attach roles
            $user->roles()->attach($request['roles']);
            // Attach companies
            $user->companies()->attach($request['companies']);
            $customer->update($request->all());
        }
        $message = trans('messages.success.updated', ['type' => trans_choice('general.customers', 1)]);
        flash($message)->success();
        return redirect('incomes/customers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Customer $customer
     *
     * @return Response
     */
    public function destroy(Customer $customer)
    {
        $relationships = $this->countRelationships($customer, [
          'invoices' => 'invoices',
          'revenues' => 'revenues',
        ]);
        if (empty($relationships)) {
            $customer->delete();
            $message = trans('messages.success.deleted', ['type' => trans_choice('general.customers', 1)]);
            flash($message)->success();
        } else {
            $message = trans('messages.warning.deleted',
              ['name' => $customer->name, 'text' => implode(', ', $relationships)]);
            flash($message)->warning();
        }
        return redirect('incomes/customers');
    }

    public function currency()
    {
        $customer_id = request('customer_id');
        $customer = Customer::find($customer_id);
        return response()->json($customer);
    }
}
