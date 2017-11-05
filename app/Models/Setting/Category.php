<?php
namespace App\Models\Setting;

use App\Models\Model;

class Category extends Model
{
    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'type', 'enabled'];
    protected $table = 'categories';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'name', 'type', 'color', 'enabled'];

    public function revenues()
    {
        return $this->hasMany('App\Models\Income\Revenue');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Expense\Payment');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Item\Item');
    }

    /**
     * Scope to only include categories of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
