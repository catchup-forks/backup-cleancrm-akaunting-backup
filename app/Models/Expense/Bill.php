<?php
namespace App\Models\Expense;

use App\Models\Model;
use App\Traits\Currencies;
use App\Traits\DateTime;
use Sofa\Eloquence\Eloquence;

class Bill extends Model
{
    use Currencies, DateTime, Eloquence;

    protected $table = 'bills';

    protected $dates = ['deleted_at', 'billed_at', 'due_at'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
      'company_id',
      'bill_number',
      'order_number',
      'bill_status_code',
      'billed_at',
      'due_at',
      'amount',
      'currency_code',
      'currency_rate',
      'vendor_id',
      'vendor_name',
      'vendor_email',
      'vendor_tax_number',
      'vendor_phone',
      'vendor_address',
      'notes',
      'attachment'
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['bill_number', 'vendor_name', 'amount', 'status.name', 'billed_at', 'due_at'];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchableColumns = [
      'bill_number' => 10,
      'order_number' => 10,
      'vendor_name' => 10,
      'vendor_email' => 5,
      'vendor_phone' => 2,
      'vendor_address' => 1,
      'notes' => 2,
    ];

    public function vendor()
    {
        return $this->belongsTo('App\Models\Expense\Vendor');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Setting\Currency', 'currency_code', 'code');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Expense\BillStatus', 'bill_status_code', 'code');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Expense\BillItem');
    }

    public function totals()
    {
        return $this->hasMany('App\Models\Expense\BillTotal');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Expense\BillPayment');
    }

    public function histories()
    {
        return $this->hasMany('App\Models\Expense\BillHistory');
    }

    /**
     * Convert amount to double.
     *
     * @param  string $value
     * @return void
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (double)$value;
    }

    /**
     * Convert currency rate to double.
     *
     * @param  string $value
     * @return void
     */
    public function setCurrencyRateAttribute($value)
    {
        $this->attributes['currency_rate'] = (double)$value;
    }

    public function scopeDue($query, $date)
    {
        return $query->where('due_at', '=', $date);
    }
}
