<?php
namespace App\Models\Expense;

use App\Models\Model;
use Sofa\Eloquence\Eloquence;

class Vendor extends Model
{
    use Eloquence;

    protected $table = 'vendors';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
      'company_id',
      'name',
      'email',
      'tax_number',
      'phone',
      'address',
      'website',
      'currency_code',
      'enabled'
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'email', 'phone', 'enabled'];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchableColumns = [
      'name' => 10,
      'email' => 5,
      'phone' => 2,
      'website' => 2,
      'address' => 1,
    ];

    public function bills()
    {
        return $this->hasMany('App\Models\Expense\Bill');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Expense\Payment');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Setting\Currency', 'currency_code', 'code');
    }
}
