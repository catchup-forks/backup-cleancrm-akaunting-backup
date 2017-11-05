<?php
namespace App\Models\Auth;

use App\Notifications\Auth\Reset;
use Date;
use EloquentFilter\Filterable;
use GuzzleHttp\Exception\RequestException;
use Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Kyslik\ColumnSortable\Sortable;
use Request;
use Route;

class User extends Authenticatable
{
    use Filterable, LaratrustUserTrait, Notifiable, SoftDeletes, Sortable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'locale', 'picture', 'enabled'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_logged_in_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'email', 'enabled'];

    public function companies()
    {
        return $this->morphToMany('App\Models\Company\Company', 'user', 'user_companies', 'user_id', 'company_id');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Income\Customer', 'user_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Income\Invoice', 'customer_id', 'id');
    }

    public function revenues()
    {
        return $this->hasMany('App\Models\Income\Revenue', 'customer_id', 'id');
    }

    /**
     * Always capitalize the name when we retrieve it
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Always return a valid picture when we retrieve it
     */
    public function getPictureAttribute($value)
    {
        // Check if we should use gravatar
        if (setting('general.use_gravatar', '0') == '1') {
            // Check for gravatar
            $url = 'https://www.gravatar.com/avatar/' . md5(strtolower($this->getAttribute('email'))) . '?size=90&d=404';
            $client = new \GuzzleHttp\Client(['verify' => false]);
            try {
                $value = $client->request('GET', $url)->getBody()->getContents();
            } catch (RequestException $e) {
                // 404 Not Found
            }
        }
        return $value;
    }

    /**
     * Always return a valid picture when we retrieve it
     */
    public function getLastLoggedInAtAttribute($value)
    {
        // Date::setLocale('tr');
        if (!empty($value)) {
            return Date::parse($value)->diffForHumans();
        } else {
            return trans('auth.never');
        }
    }

    /**
     * Send reset link to user via email
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new Reset($token));
    }

    /**
     * Always capitalize the name when we save it to the database
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
    }

    /**
     * Always hash the password when we save it to the database
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Define the filter provider globally.
     *
     * @return ModelFilter
     */
    public function modelFilter()
    {
        // Check if is api or web
        if (Request::is('api/*')) {
            $arr = array_reverse(explode('\\', explode('@', app()['api.router']->currentRouteAction())[0]));
            $folder = $arr[1];
            $file = $arr[0];
        } else {
            list($folder, $file) = explode('/', Route::current()->uri());
        }
        if (empty($folder) || empty($file)) {
            return $this->provideFilter();
        }
        //$class = '\App\Filters\Auth\Users';
        $class = '\App\Filters\\' . ucfirst($folder) . '\\' . ucfirst($file);
        return $this->provideFilter($class);
    }

    /**
     * Scope to get all rows filtered, sorted and paginated.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $sort
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCollect($query, $sort = 'name')
    {
        $request = request();
        $input = $request->input();
        $limit = $request->get('limit', setting('general.list_limit', '25'));
        return $this->filter($input)->sortable($sort)->paginate($limit);
    }

    /**
     * Scope to only include active currencies.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }
}
