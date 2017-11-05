<?php
namespace App\Models\Income;

use App\Models\Model;
use App\Traits\Currencies;
use App\Traits\DateTime;
use Sofa\Eloquence\Eloquence;

class Revenue extends Model
{
    use Currencies, DateTime, Eloquence;

    protected $table = 'revenues';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
      'company_id',
      'bankaccount_id',
      'paid_at',
      'amount',
      'currency_code',
      'currency_rate',
      'customer_id',
      'description',
      'category_id',
      'payment_method',
      'reference',
      'attachment'
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['paid_at', 'amount', 'category_id', 'account', 'payment_method'];

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
      'notes' => 2,
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User', 'customer_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Banking\Account');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Setting\Currency', 'currency_code', 'code');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Setting\Category');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Income\Customer');
    }

    public function transfers()
    {
        return $this->hasMany('App\Models\Banking\Transfer');
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

    public function scopeLatest($query)
    {
        return $query->orderBy('paid_at', 'desc');
    }
}
