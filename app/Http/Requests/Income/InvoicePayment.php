<?php
namespace App\Http\Requests\Income;

use App\Http\Requests\Request;

class InvoicePayment extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'bankaccount_id' => 'required|integer',
          'paid_at' => 'required|date',
          'amount' => 'required',
          'currency_code' => 'required|string',
          'payment_method' => 'required|string',
          'attachment' => 'mimes:jpeg,jpg,png,pdf',
        ];
    }
}
