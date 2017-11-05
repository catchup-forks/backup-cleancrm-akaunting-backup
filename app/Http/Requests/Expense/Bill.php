<?php
namespace App\Http\Requests\Expense;

use App\Http\Requests\Request;

class Bill extends Request
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
        // Check if store or update
        if ($this->getMethod() == 'PATCH') {
            $id = $this->bill->getAttribute('id');
        } else {
            $id = null;
        }
        // Get company id
        $company_id = $this->request->get('company_id');
        return [
          'vendor_id' => 'required|integer',
          'bill_number' => 'required|string|unique:bills,NULL,' . $id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
          'billed_at' => 'required|date',
          'due_at' => 'required|date',
          'currency_code' => 'required|string',
          'attachment' => 'mimes:' . setting('general.file_types') . '|between:0,' . setting('general.file_size') * 1024,
        ];
    }
}
