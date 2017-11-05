<?php
namespace App\Utilities;

use DB;
use App\Models\Company\Company;

class Info
{

    public static function versions()
    {
        $v = array();
        $v['akaunting'] = version('short');
        $v['php'] = static::phpVersion();
        $v['mysql'] = static::mysqlVersion();
        return $v;
    }

    public static function all()
    {
        $data = static::versions();
        $data['token'] = setting('general.token');
        $data['companies'] = Company::all()->count();
        return $data;
    }

    public static function phpVersion()
    {
        return phpversion();
    }

    public static function mysqlVersion()
    {
        return DB::selectOne('select version() as mversion')->mversion;
    }
}