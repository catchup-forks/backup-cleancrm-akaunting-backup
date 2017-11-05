<?php
namespace App\Traits;

use ClickNow\Money\Money;
use ClickNow\Money\Currency;

trait Currencies
{

    public function convert($amount, $code, $rate, $format = false)
    {
        $default = new Currency(setting('general.default_currency', 'USD'));
        if ($format) {
            $money = Money::$code($amount, true)->convert($default, $rate)->format();
        } else {
            $money = Money::$code($amount)->convert($default, $rate)->getAmount();
        }
        return $money;
    }

    public function reverseConvert($amount, $code, $rate, $format = false)
    {
        $default = setting('general.default_currency', 'USD');
        $code = new Currency($code);
        if ($format) {
            $money = Money::$default($amount, true)->convert($code, $rate)->format();
        } else {
            $money = Money::$default($amount)->convert($code, $rate)->getAmount();
        }
        return $money;
    }

    public function dynamicConvert($default, $amount, $code, $rate, $format = false)
    {
        $code = new Currency($code);
        if ($format) {
            $money = Money::$default($amount, true)->convert($code, $rate)->format();
        } else {
            $money = Money::$default($amount)->convert($code, $rate)->getAmount();
        }
        return $money;
    }

    public function getConvertedAmount($format = false)
    {
        return $this->convert($this->amount, $this->currency_code, $this->currency_rate, $format);
    }

    public function getReverseConvertedAmount($format = false)
    {
        return $this->reverseConvert($this->amount, $this->currency_code, $this->currency_rate, $format);
    }

    public function getDynamicConvertedAmount($format = false)
    {
        return $this->dynamicConvert($this->default_currency_code, $this->amount, $this->currency_code,
          $this->currency_rate, $format);
    }
}