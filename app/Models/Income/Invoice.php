<?php
namespace App\Models\Income;

use App\Models\Model;
use App\Traits\Currencies;
use App\Traits\DateTime;
use Sofa\Eloquence\Eloquence;

class Invoice extends Model
{
    use Currencies, DateTime, Eloquence;

    protected $table = 'invoices';

    protected $dates = ['deleted_at', 'invoiced_at', 'due_at'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
      'company_id',
      'invoice_number',
      'order_number',
      'invoice_status_code',
      'invoiced_at',
      'due_at',
      'amount',
      'currency_code',
      'currency_rate',
      'customer_id',
      'customer_name',
      'customer_email',
      'customer_tax_number',
      'customer_phone',
      'customer_address',
      'notes',
      'attachment'
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['invoice_number', 'customer_name', 'amount', 'status', 'invoiced_at', 'due_at'];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchableColumns = [
      'invoice_number' => 10,
      'order_number' => 10,
      'customer_name' => 10,
      'customer_email' => 5,
      'customer_phone' => 2,
      'customer_address' => 1,
      'notes' => 2,
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User', 'customer_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Income\Customer');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Setting\Currency', 'currency_code', 'code');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Income\InvoiceStatus', 'invoice_status_code', 'code');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Income\InvoiceItem');
    }

    public function totals()
    {
        return $this->hasMany('App\Models\Income\InvoiceTotal');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Income\InvoicePayment');
    }

    public function histories()
    {
        return $this->hasMany('App\Models\Income\InvoiceHistory');
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
