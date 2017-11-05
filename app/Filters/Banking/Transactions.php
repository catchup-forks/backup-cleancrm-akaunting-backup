<?php
namespace App\Filters\Banking;

use EloquentFilter\ModelFilter;

class Transactions extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relatedModel => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function account($account_id)
    {
        return $this->where('bankaccount_id', $account_id);
    }

    public function category($category_id)
    {
        return $this->where('category_id', $category_id);
    }
}