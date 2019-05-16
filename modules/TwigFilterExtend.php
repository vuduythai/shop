<?php

namespace Modules;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Currency;

class TwigFilterExtend extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('displayPriceAndCurrency', array($this, 'displayPriceAndCurrency')),
            new TwigFilter('strPad', array($this, 'strPad')),
        );
    }

    public function displayPriceAndCurrency($price)
    {
        $currency = Config::getCurrencySymbol();
        if ($currency['symbol_position'] == Currency::POSITION_BEFORE) {//before
            return $currency['symbol']. ' ' .$price;
        } else {//after
            return $price. ' ' . $currency['symbol'];
        }
    }

    /**
     * Display order id
     */
    public function strPad($text)
    {
        return str_pad($text, 10, '0', STR_PAD_LEFT);
    }
}