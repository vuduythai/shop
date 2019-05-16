<?php

namespace Modules\Frontend\Classes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Backend\Core\System;
use Modules\Backend\Models\TaxRate;
use Modules\Backend\Models\TaxRule;

class Price extends Model
{
    /**
     * Get all tax
     */
    public static function getTax()
    {
        $taxRule = TaxRule::select('tax_class_id', 'tax_rate_id')
            ->get()
            ->toArray();
        $taxRate = TaxRate::select('id', 'type', 'rate')
            ->get()
            ->toArray();
        $taxRateConvert = [];
        foreach ($taxRate as $row) {
            $taxRateConvert[$row['id']] = [
                'type' => $row['type'],
                'rate' => $row['rate']
            ];
        }
        $taxRuleConvert = [];
        foreach ($taxRule as $row) {
            $taxRuleConvert[$row['tax_class_id']][] = $row['tax_rate_id'];
        }
        $taxClassArray = [];
        foreach ($taxRuleConvert as $key => $value) {
            $array = [];
            foreach ($value as $v) {
                if (array_key_exists($v, $taxRateConvert)) {
                    $array[] = $taxRateConvert[$v];
                } else {
                    $array[] = '';
                }
            }
            $taxClassArray[$key] = $array;
        }
        return $taxClassArray;
    }

    /**
     * Calculate price with tax
     */
    public static function finalPrice($price, $taxClassId, $taxClassArray)
    {
        $finalPrice = $price;
        if (array_key_exists($taxClassId, $taxClassArray)) {
            $taxClassForPrice = $taxClassArray[$taxClassId];
            foreach ($taxClassForPrice as $row) {
                if ($row['type'] == System::TYPE_PERCENTAGE && $row['rate'] != 0) {
                    $finalPrice += ($row['rate'] / 100) * $price;
                }
                if ($row['type'] == System::TYPE_FIX_AMOUNT) {
                    $finalPrice += $row['rate'];
                }
            }
        }
        return $finalPrice;
    }

    /**
     * Get price to calculate Tax
     */
    public static function getPriceToCalculateTax($row)
    {
        $price = $row->price;
        $displayPricePromotion = System::NO;
        $promotion = $row->price_promotion;
        $from = $row->price_promo_from;
        $to = $row->price_promo_to;
        if (!empty($promotion) && empty($from) && empty($to)) {
            $price = $promotion;
            $displayPricePromotion = System::YES;
        }
        if (!empty($promotion) && !empty($from) && empty($to)) {
            $compare = Carbon::now()->greaterThan(Carbon::createFromFormat('Y-m-d H:i:s', $from));
            if ($compare == true) {
                $price = $promotion;
                $displayPricePromotion = System::YES;
            }
        }
        if (!empty($promotion) && empty($from) && !empty($to)) {
            $compare = Carbon::now()->lessThan(Carbon::createFromFormat('Y-m-d H:i:s', $to));
            if ($compare == true) {
                $price = $promotion;
                $displayPricePromotion = System::YES;
            }
        }
        if (!empty($promotion) && !empty($from) && !empty($to)) {
            $compareFrom = Carbon::now()->greaterThan(Carbon::createFromFormat('Y-m-d H:i:s', $from));
            $compareTo = Carbon::now()->lessThan(Carbon::createFromFormat('Y-m-d H:i:s', $to));
            if ($compareFrom == true && $compareTo == true) {
                $price = $promotion;
                $displayPricePromotion = System::YES;
            }
        }
        return $rs = [
            'price' => $price,
            'display_price_promotion' =>$displayPricePromotion
        ];
    }

    /**
     * add final_price to product object
     * output : object
     */
    public static function addFinalPriceForProductObject($data)
    {
        $taxClassArray = self::getTax();
        foreach ($data as $row) {
            $priceRs = self::getPriceToCalculateTax($row);
            $row->final_price = self::finalPrice($priceRs['price'], $row->tax_class_id, $taxClassArray);
            $row->display_price_promotion = $priceRs['display_price_promotion'];
        }
        return $data;
    }

    /**
     * add final_price for detail product
     * output: object
     */
    public static function addFinalPriceForDetailProduct($data)
    {
        $taxClassArray = self::getTax();
        $priceRs = self::getPriceToCalculateTax($data);
        $data->final_price = self::finalPrice($priceRs['price'], $data->tax_class_id, $taxClassArray);
        $data->display_price_promotion = $priceRs['display_price_promotion'];
        return $data;
    }

}