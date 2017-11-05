<?php
namespace App\Http\Requests\Income;

use App\Http\Requests\Request;

class Customer extends Request
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
        $required = '';
        // Check if store or update
        if ($this->getMethod() == 'PATCH') {
            $id = $this->customer->getAttribute('id');
        } else {
            $id = null;
        }
        if (!empty($this->request->get('create_user'))) {
            $required = 'required|';
        }
        // Get company id
        $company_id = $this->request->get('company_id');
        return [
          'name' => 'required|string',
          'email' => 'required|email|unique:customers,NULL,' . $id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
          'currency_code' => 'required|string',
          'password' => $required . 'confirmed',
        ];
    }
}
