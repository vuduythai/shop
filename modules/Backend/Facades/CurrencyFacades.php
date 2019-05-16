<?php
namespace Modules\Backend\Facades;

use Illuminate\Database\Eloquent\Model;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Currency;

class CurrencyFacades extends Model
{

    const NO_SERVICE = 0;
    const OPEN_EXCHANGE_RATE = 1;
    const FIXER_IO = 2;
    const CURRENCY_DATA_FEED = 3;
    const FREE_CURRENCY_CONVERT = 4;

    /**
     * Get array currency to convert
     */
    public static function getArrayCurrencyToConvert($baseCurrency)
    {
        $data = Currency::select('code')->get();
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[] = $row->code;
            }
        }
        if (count($rs) > 1) {
            $rs[$baseCurrency] = 1;
        }
        return $rs;
    }

    /**
     * Convert currency multiple based on service
     */
    public static function convertCurrencyMultiple($service, $data, $baseCurrency = 'USD', $arrayCurrencyConvert = [])
    {
        $rateArray = [];
        if ($service == self::OPEN_EXCHANGE_RATE || $service == self::FIXER_IO) {
            $rates = $data['rates'];
            foreach ($arrayCurrencyConvert as $currency) {
                if (!empty($rates[$baseCurrency]) && !empty($rates[$currency])) {
                    $rateArray[$currency] = $rates[$currency] / $rates[$baseCurrency] ;
                }
            }
        }
        if ($service == self::CURRENCY_DATA_FEED) {
            $rates = $data['currency'];
            if (!empty($rates)) {
                foreach ($rates as $rate) {
                    $rateArray[$rate['currency']] = $rate['value'];
                }
            }
        }
        if ($service == self::FREE_CURRENCY_CONVERT) {
            if (!empty($data)) {
                foreach ($data as $code => $value) {
                    $currencyConvertCode = str_replace($baseCurrency.'_', '', $code);
                    $rateArray[$currencyConvertCode] = $value;
                }
            }
        }
        return $rateArray;
    }

    /**
     * Convert array currency to string based on service
     */
    public static function convertCurrencyArrayToString($service, $baseCurrencyCode, $currencyArrayConvert)
    {
        $currencyList = '';
        if ($service == self::CURRENCY_DATA_FEED) {
            if (!empty($currencyArrayConvert)) {
                $currencyList = implode('+', $currencyArrayConvert);
            }
        }
        if ($service == self::FREE_CURRENCY_CONVERT) {
            if (!empty($currencyArrayConvert)) {
                foreach ($currencyArrayConvert as $row) {
                    $currencyList .= $baseCurrencyCode.'_'.$row.',';
                }
            }
            if (!empty($currencyList)) {
                $currencyList = substr($currencyList, 0, -1);
            }
        }
        return $currencyList;
    }

    /**
     * Get url based on service
     */
    public static function getUrlBasedOnService($service, $baseCurrencyCode, $apiKey, $currencyList)
    {
        switch ($service) {
            case self::OPEN_EXCHANGE_RATE:
                $url = 'https://openexchangerates.org/api/latest.json?app_id='.$apiKey;
                break;

            case self::FIXER_IO:
                $url = 'http://data.fixer.io/api/latest?access_key='.$apiKey;
                break;

            case self::CURRENCY_DATA_FEED:
                $url = 'https://currencydatafeed.com/api/source_currency.php?token='.
                    $apiKey.'&source='.$baseCurrencyCode.'&target='.$currencyList;
                break;

            case self::FREE_CURRENCY_CONVERT:
                $url = 'https://free.currencyconverterapi.com/api/v6/convert?q='.
                    $currencyList.'&compact=ultra&apiKey='.$apiKey;
                break;

            default:
                $url = '';
        }
        return $url;
    }

    /**
     * Re update currency rate
     */
    public static function reUpdateCurrencyRate($currencyRateArray)
    {
        if (!empty($currencyRateArray)) {
            foreach ($currencyRateArray as $code => $value) {
                Currency::where('code', $code)->update(['value'=>$value]);
            }
        }
    }

    /**
     * Handle response error
     */
    public static function handleResponseError($service, $response)
    {
        $responseArray = json_decode($response, true);
        if ($service == self::OPEN_EXCHANGE_RATE) {
            if (!empty($responseArray['error'])) {
                return ['rs'=>System::FAIL, 'msg'=>[$responseArray['description']]];
            }
        }
        if ($service == self::FIXER_IO) {
            if ($responseArray['success'] == false) {
                return ['rs'=>System::FAIL, 'msg'=>[$responseArray['error']['info']]];
            }
        }
        if ($service == self::CURRENCY_DATA_FEED) {
            if ($responseArray['status'] == false) {
                return ['rs'=>System::FAIL, 'msg'=>[__('Backend.Lang::lang.msg.api_wrong')]];
            }
        }
        if ($service == self::FREE_CURRENCY_CONVERT) {
            if (!empty($responseArray['status'])) {
                return ['rs'=>System::FAIL, 'msg'=>[$responseArray['error']]];
            }
        }
        return ['rs'=>System::SUCCESS, 'msg'=>[]];
    }

    /**
     * Convert currency
     */
    public static function convertCurrency($close)
    {
        try {
            $config = Config::getConfigByKey('config', '');
            $configArray = json_decode($config, true);
            $baseCurrency = Config::getCurrencySymbol();
            $baseCurrencyCode = $baseCurrency['code'];
            $service = isset($configArray['currency_service']) ? $configArray['currency_service'] : self::NO_SERVICE;
            if ($service == self::NO_SERVICE) {
                return ['rs'=>System::FAIL, 'msg'=>[__('Backend.Lang::lang.msg.no_currency_convert_service')]];
            }
            $apiKey = isset($configArray['currency_service_api']) ? $configArray['currency_service_api'] : '';
            $currencyArrayConvert = self::getArrayCurrencyToConvert($baseCurrencyCode);
            $currencyList = self::convertCurrencyArrayToString($service, $baseCurrencyCode, $currencyArrayConvert);
            $url = self::getUrlBasedOnService($service, $baseCurrencyCode, $apiKey, $currencyList);
            $cSession = curl_init();
            curl_setopt($cSession, CURLOPT_URL, $url);
            curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cSession, CURLOPT_SSL_VERIFYPEER, true);
            $response = curl_exec($cSession);
            curl_close($cSession);
            $rs = self::handleResponseError($service, $response);
            if ($rs['rs'] == System::FAIL) {
                return $rs;
            }
            $data = json_decode($response, true);
            $currencyRateArray = self::convertCurrencyMultiple(
                $service,
                $data,
                $baseCurrencyCode,
                $currencyArrayConvert
            );
            self::reUpdateCurrencyRate($currencyRateArray);
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }

    /**
     * Select currency convert
     */
    public static function selectCurrencyConvertService()
    {
        return [
            self::NO_SERVICE => __('Backend.Lang::lang.currency.no_service'),
            self::OPEN_EXCHANGE_RATE => 'https://openexchangerates.org',
            self::FIXER_IO => 'https://fixer.io',
            self::CURRENCY_DATA_FEED => 'https://currencydatafeed.com',
            self::FREE_CURRENCY_CONVERT => 'https://free.currencyconverterapi.com'
        ];
    }
}




