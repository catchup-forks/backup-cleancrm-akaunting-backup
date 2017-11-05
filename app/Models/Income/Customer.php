<?php
namespace App\Models\Income;

use App\Models\Model;
use Illuminate\Notifications\Notifiable;
use Sofa\Eloquence\Eloquence;

class Customer extends Model
{
    use Eloquence;
    use Notifiable;

    protected $table = 'customers';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
      'company_id',
      'staff_id',
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

    public function invoices()
    {
        return $this->hasMany('App\Models\Income\Invoice');
    }

    public function revenues()
    {
        return $this->hasMany('App\Models\Income\Revenue');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Setting\Currency', 'currency_code', 'code');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User', 'customer_id', 'id');
    }
}
