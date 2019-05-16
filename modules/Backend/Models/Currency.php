<?php

namespace Modules\Backend\Models;

use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\BaseForm;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;

class Currency extends AppModel
{
    protected $table = 'currency';

    const POSITION_BEFORE = 0;
    const POSITION_AFTER = 1;

    /**
     * key 'relation' is a string: 'relation,field_name'
     */
    public static function getList($params)
    {
        $query = self::orderBy('id', 'desc');
        if (isset($params['key'])) {
            $query->where('name', 'like', '%'.$params['key'].'%');
        }
        $data = $query->paginate(System::PAGE_SIZE_DEFAULT);
        $field = [
            ['column'=>'id', 'name'=>__('Backend.Lang::lang.field.id')],
            ['column'=>'name', 'name'=>__('Backend.Lang::lang.field.name')],
            ['column'=>'code', 'name'=>__('Backend.Lang::lang.field.code')],
            ['column'=>'symbol', 'name'=>__('Backend.Lang::lang.field.symbol')],
            ['column'=>'symbol_position', 'name'=>__('Backend.Lang::lang.field.symbol_position')],
            ['column'=>'value', 'name'=>__('Backend.Lang::lang.field.value')]
        ];
        $data = self::convertSymbolPosition($data);
        $rs = [
            'data' => $data,
            'field' => $field,
            'button' => ['Backend.View::group.currency.buttonConvert']
        ];
        return $rs;
    }

    public static function convertSymbolPosition($obj)
    {
        foreach ($obj as $row) {
            switch ($row->symbol_position) {
                case System::POSITION_BEFORE:
                    $row->symbol_position = __('Backend.Lang::lang.general.before');
                    break;
                case System::POSITION_AFTER:
                    $row->symbol_position = __('Backend.Lang::lang.general.after');
                    break;
                default:
                    $row->symbol_position = __('Backend.Lang::lang.general.after');
            }
        }
        return $obj;
    }
    /**
     * Return form to create and edit
     */
    public static function formCreate($request, $controller, $id = '')
    {
        $data = new \stdClass();
        if ($id != '') {//edit
            $data = Currency::find($id);
        }
        $code = Currency::getCurrencyCode();
        $symbolPosition = Currency::getPosition();
        $arrayField = [
            ['text', 'name', [], System::YES],
            ['text', 'symbol', [], System::YES],
            ['text', 'value', []],
            ['select', 'code', $code],
            ['select', 'symbol_position', $symbolPosition],
        ];
        $form = BaseForm::generateForm($data, $controller, $arrayField);
        $form['id'] = !empty($data->id) ? $data->id : '';
        return $form;
    }

    /**
     * Validate data
     */
    public static function validateDataThenSave($data, $controller, $close)
    {
        $msgValidate = [];
        $rule = [
            'name' => 'required',
            'symbol' => 'required'
        ];
        return AppModel::returnValidateResult($data, $rule, $msgValidate, $controller, $close);
    }

    /**
     * Save
     */
    public static function saveRecord($data, $close)
    {
        try {
            $id = $data['id'];
            $model = new Currency();
            if ($id != 0) {//edit
                $model = self::find($id);
            }
            $model->name = $data['name'];
            $model->code = $data['code'];
            $model->symbol = $data['symbol'];
            $model->value = $data['value'];
            $model->symbol_position = $data['symbol_position'];
            $model->save();
            return ['rs'=>System::SUCCESS, 'msg'=>'', 'id'=>$model->id, 'closeRs'=>$close];
        } catch (\Exception $e) {
            return ['rs'=>System::FAIL, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * Return symbol position text
     */
    public static function getPosition()
    {
        $data = [
            System::POSITION_BEFORE => __('Backend.Lang::lang.general.before'),
            System::POSITION_AFTER => __('Backend.Lang::lang.general.after')
        ];
        return $data;
    }

    /**
     * Return currency code from google
     */
    public static function getCurrencyCode()
    {
        $code = [
            'AED' => 'United Arab Emirates Dirham (AED)',
            'AFN' => 'Afghan Afghani (AFN)',
            'ALL' => 'Albanian Lek (ALL)',
            'AMD' => 'Armenian Dram (AMD)',
            'ANG' => 'Netherlands Antillean Guilder (ANG)',
            'AOA' => 'Angolan Kwanza (AOA)',
            'ARS' => 'Argentine Peso (ARS)',
            'AUD' => 'Australian Dollar (A$)',
            'AWG' => 'Aruban Florin (AWG)',
            'AZN' => 'Azerbaijani Manat (AZN)',
            'BAM' => 'Bosnia-Herzegovina Convertible Mark (BAM)',
            'BBD' => 'Barbadian Dollar (BBD)',
            'BDT' => 'Bangladeshi Taka (BDT)',
            'BGN' => 'Bulgarian Lev (BGN)',
            'BHD' => 'Bahraini Dinar (BHD)',
            'BIF' => 'Burundian Franc (BIF)',
            'BMD' => 'Bermudan Dollar (BMD)',
            'BND' => 'Brunei Dollar (BND)',
            'BOB' => 'Bolivian Boliviano (BOB)',
            'BRL' => 'Brazilian Real (R$)',
            'BSD' => 'Bahamian Dollar (BSD)',
            'BTC' => 'Bitcoin (฿)',
            'BTN' => 'Bhutanese Ngultrum (BTN)',
            'BWP' => 'Botswanan Pula (BWP)',
            'BYN' => 'Belarusian Ruble (BYN)',
            'BYR' => 'Belarusian Ruble (2000–2016) (BYR)',
            'BZD' => 'Belize Dollar (BZD)',
            'CAD' => 'Canadian Dollar (CA$)',
            'CDF' => 'Congolese Franc (CDF)',
            'CHF' => 'Swiss Franc (CHF)',
            'CLF' => 'Chilean Unit of Account (UF) (CLF)',
            'CLP' => 'Chilean Peso (CLP)',
            'CNH' => 'CNH (CNH)',
            'CNY' => 'Chinese Yuan (CN¥)',
            'COP' => 'Colombian Peso (COP)',
            'CRC' => 'Costa Rican Colón (CRC)',
            'CUP' => 'Cuban Peso (CUP)',
            'CVE' => 'Cape Verdean Escudo (CVE)',
            'CZK' => 'Czech Republic Koruna (CZK)',
            'DEM' => 'German Mark (DEM)',
            'DJF' => 'Djiboutian Franc (DJF)',
            'DKK' => 'Danish Krone (DKK)',
            'DOP' => 'Dominican Peso (DOP)',
            'DZD' => 'Algerian Dinar (DZD)',
            'EGP' => 'Egyptian Pound (EGP)',
            'ERN' => 'Eritrean Nakfa (ERN)',
            'ETB' => 'Ethiopian Birr (ETB)',
            'EUR' => 'Euro (€)',
            'FIM' => 'Finnish Markka (FIM)',
            'FJD' => 'Fijian Dollar (FJD)',
            'FKP' => 'Falkland Islands Pound (FKP)',
            'FRF' => 'French Franc (FRF)',
            'GBP' => 'British Pound (£)',
            'GEL' => 'Georgian Lari (GEL)',
            'GHS' => 'Ghanaian Cedi (GHS)',
            'GIP' => 'Gibraltar Pound (GIP)',
            'GMD' => 'Gambian Dalasi (GMD)',
            'GNF' => 'Guinean Franc (GNF)',
            'GTQ' => 'Guatemalan Quetzal (GTQ)',
            'GYD' => 'Guyanaese Dollar (GYD)',
            'HKD' => 'Hong Kong Dollar (HK$)',
            'HNL' => 'Honduran Lempira (HNL)',
            'HRK' => 'Croatian Kuna (HRK)',
            'HTG' => 'Haitian Gourde (HTG)',
            'HUF' => 'Hungarian Forint (HUF)',
            'IDR' => 'Indonesian Rupiah (IDR)',
            'IEP' => 'Irish Pound (IEP)',
            'ILS' => 'Israeli New Shekel (₪)',
            'INR' => 'Indian Rupee (₹)',
            'IQD' => 'Iraqi Dinar (IQD)',
            'IRR' => 'Iranian Rial (IRR)',
            'ISK' => 'Icelandic Króna (ISK)',
            'ITL' => 'Italian Lira (ITL)',
            'JMD' => 'Jamaican Dollar (JMD)',
            'JOD' => 'Jordanian Dinar (JOD)',
            'JPY' => 'Japanese Yen (¥)',
            'KES' => 'Kenyan Shilling (KES)',
            'KGS' => 'Kyrgystani Som (KGS)',
            'KHR' => 'Cambodian Riel (KHR)',
            'KMF' => 'Comorian Franc (KMF)',
            'KPW' => 'North Korean Won (KPW)',
            'KRW' => 'South Korean Won (₩)',
            'KWD' => 'Kuwaiti Dinar (KWD)',
            'KYD' => 'Cayman Islands Dollar (KYD)',
            'KZT' => 'Kazakhstani Tenge (KZT)',
            'LAK' => 'Laotian Kip (LAK)',
            'LBP' => 'Lebanese Pound (LBP)',
            'LKR' => 'Sri Lankan Rupee (LKR)',
            'LRD' => 'Liberian Dollar (LRD)',
            'LSL' => 'Lesotho Loti (LSL)',
            'LTL' => 'Lithuanian Litas (LTL)',
            'LVL' => 'Latvian Lats (LVL)',
            'LYD' => 'Libyan Dinar (LYD)',
            'MAD' => 'Moroccan Dirham (MAD)',
            'MDL' => 'Moldovan Leu (MDL)',
            'MGA' => 'Malagasy Ariary (MGA)',
            'MKD' => 'Macedonian Denar (MKD)',
            'MMK' => 'Myanmar Kyat (MMK)',
            'MNT' => 'Mongolian Tugrik (MNT)',
            'MOP' => 'Macanese Pataca (MOP)',
            'MRO' => 'Mauritanian Ouguiya (MRO)',
            'MUR' => 'Mauritian Rupee (MUR)',
            'MVR' => 'Maldivian Rufiyaa (MVR)',
            'MWK' => 'Malawian Kwacha (MWK)',
            'MXN' => 'Mexican Peso (MX$)',
            'MYR' => 'Malaysian Ringgit (MYR)',
            'MZN' => 'Mozambican Metical (MZN)',
            'NAD' => 'Namibian Dollar (NAD)',
            'NGN' => 'Nigerian Naira (NGN)',
            'NIO' => 'Nicaraguan Córdoba (NIO)',
            'NOK' => 'Norwegian Krone (NOK)',
            'NPR' => 'Nepalese Rupee (NPR)',
            'NZD' => 'New Zealand Dollar (NZ$)',
            'OMR' => 'Omani Rial (OMR)',
            'PAB' => 'Panamanian Balboa (PAB)',
            'PEN' => 'Peruvian Sol (PEN)',
            'PGK' => 'Papua New Guinean Kina (PGK)',
            'PHP' => 'Philippine Peso (PHP)',
            'PKG' => 'PKG (PKG)',
            'PKR' => 'Pakistani Rupee (PKR)',
            'PLN' => 'Polish Zloty (PLN)',
            'PYG' => 'Paraguayan Guarani (PYG)',
            'QAR' => 'Qatari Rial (QAR)',
            'RON' => 'Romanian Leu (RON)',
            'RSD' => 'Serbian Dinar (RSD)',
            'RUB' => 'Russian Ruble (RUB)',
            'RWF' => 'Rwandan Franc (RWF)',
            'SAR' => 'Saudi Riyal (SAR)',
            'SBD' => 'Solomon Islands Dollar (SBD)',
            'SCR' => 'Seychellois Rupee (SCR)',
            'SDG' => 'Sudanese Pound (SDG)',
            'SEK' => 'Swedish Krona (SEK)',
            'SGD' => 'Singapore Dollar (SGD)',
            'SHP' => 'St. Helena Pound (SHP)',
            'SKK' => 'Slovak Koruna (SKK)',
            'SLL' => 'Sierra Leonean Leone (SLL)',
            'SOS' => 'Somali Shilling (SOS)',
            'SRD' => 'Surinamese Dollar (SRD)',
            'STD' => 'São Tomé &amp; Príncipe Dobra (STD)',
            'SVC' => 'Salvadoran Colón (SVC)',
            'SYP' => 'Syrian Pound (SYP)',
            'SZL' => 'Swazi Lilangeni (SZL)',
            'THB' => 'Thai Baht (THB)',
            'TJS' => 'Tajikistani Somoni (TJS)',
            'TMT' => 'Turkmenistani Manat (TMT)',
            'TND' => 'Tunisian Dinar (TND)',
            'TOP' => 'Tongan Paʻanga (TOP)',
            'TRY' => 'Turkish Lira (TRY)',
            'TTD' => 'Trinidad &amp; Tobago Dollar (TTD)',
            'TWD' => 'New Taiwan Dollar (NT$)',
            'TZS' => 'Tanzanian Shilling (TZS)',
            'UAH' => 'Ukrainian Hryvnia (UAH)',
            'UGX' => 'Ugandan Shilling (UGX)',
            'USD' => 'US Dollar ($)',
            'UYU' => 'Uruguayan Peso (UYU)',
            'UZS' => 'Uzbekistani Som (UZS)',
            'VEF' => 'Venezuelan Bolívar (VEF)',
            'VND' => 'Vietnamese Dong (₫)',
            'VUV' => 'Vanuatu Vatu (VUV)',
            'WST' => 'Samoan Tala (WST)',
            'XAF' => 'Central African CFA Franc (FCFA)',
            'XCD' => 'East Caribbean Dollar (EC$)',
            'XDR' => 'Special Drawing Rights (XDR)',
            'XOF' => 'West African CFA Franc (CFA)',
            'XPF' => 'CFP Franc (CFPF)',
            'YER' => 'Yemeni Rial (YER)',
            'ZAR' => 'South African Rand (ZAR)',
            'ZMK' => 'Zambian Kwacha (1968–2012) (ZMK)',
            'ZMW' => 'Zambian Kwacha (ZMW)',
            'ZWL' => 'Zimbabwean Dollar (2009) (ZWL)'
        ];
        return $code;
    }

    /**
     * Currency select
     */
    public static function currencySelect()
    {
        $data = self::select('id', 'name')->get();
        $rs = Functions::convertArrayKeyValue($data, 'id', 'name');
        return $rs;
    }

    /**
     * Get currency by id
     */
    public static function getCurrencyById($id)
    {
        return self::find($id);
    }

}