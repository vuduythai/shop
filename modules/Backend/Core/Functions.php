<?php
/**
 * Helper functions
 */
namespace Modules\Backend\Core;

class Functions
{
    /**
     * $data is object
     * Convert array to ['id'=>'name']
     */
    public static function convertArrayKeyValue($data, $key, $value)
    {
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[$row->$key] = $row->$value;
            }
        }
        return $rs;
    }

    /**
     *  $data is object
     * Convert object just get value
     */
    public static function convertObjectValue($data, $value)
    {
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $rs[] = $row->$value;
            }
        }
        return $rs;
    }

    /**
     * Convert object to array
     */
    public static function objectToArray($object)
    {
        $json = json_encode($object);
        $array = json_decode($json, true);
        return $array;
    }

    /**
     * Assign value for key if this key not exist in array $array
     */
    public static function assignValueIfKeyNotExists($array, $key, $value)
    {
        if (!empty($array)) {
            if (array_key_exists($key, $array)) {
                return $array[$key];
            } else {
                return $value;
            }
        }
        return $array;
    }

    /**
     * Generate random string
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    /**
     * convert name to slug
     */
    public static function convertNameToSlug($str)
    {
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|ä|å|æ',
            'd'=>'đ|ð',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị|î|ï',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ|Ä|Å|Æ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ|Ë',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị|Î|Ï',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $str);
        $slugOk = strtolower($slug);
        return $slugOk;
    }

    /**
     * Return factory model
     */
    public static function getFactoryModel($key)
    {
        $modelArray = AppModel::factoryModelBackend();
        $model = new \stdClass();
        if (array_key_exists($key, $modelArray)) {
            $model = $modelArray[$key];
        }
        return $model;
    }

    /**
     * Generate array to add 'first' class in first column of array
     */
    public static function generateArrayForFirstClass($min, $max, $numColumnPerRow)
    {
        $rs = [];
        for ($i=$min; $i<=$max; $i+=$numColumnPerRow) {
            $rs[] = $i;
        }
        return $rs;
    }

    /**
     * Assign name and value for array
     */
    public static function assignNameAndValueForArray($arrayName, $arrayData)
    {
        $rs = [];
        foreach ($arrayData as $row) {
            $data = [];
            for ($i = 0; $i<count($row); $i++) {
                $data[$arrayName[$i]] = $row[$i];
            }
            $rs[] = $data;
        }
        return $rs;
    }

    /**
     * Convert form from js
     */
    public static function convertFormFromJs($data)
    {
        $rs = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                if (preg_match('/[\[\]\']/', $row['name'])) {
                    //for example name="gallery[]"
                    $name = substr($row['name'], 0, -2);//remove [] from name
                    $rs[$name][] = $row['value'];
                } else {
                    $rs[$row['name']] = $row['value'];
                }
            }
        }
        return $rs;
    }

    /**
     * Remove \r\n\t, \r\n, \\t
     */
    public static function removeRnt($data)
    {
        $rs = [];
        foreach ($data as $row) {
            $newRow = [];
            foreach ($row as $key => $value) {
                $newRow[$key] = $value;
                if ($value != null) {
                    $newValue = str_replace('\\t', '', $value);
                    $newValue = str_replace('\r\n\t', '', $newValue);
                    $newValue = str_replace('\r\n', '', $newValue);
                    $newRow[$key] = $newValue;
                }
            }
            $rs[] = $newRow;
        }
        return $rs;
    }

    /**
     * convert array to object
     */
    public static function convertArrayToObject($data)
    {
        $object = new \stdClass();
        foreach ($data as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }

    /**
     * Sort associate array by value of a key
     * $orderBy = 'asc' or 'desc'
     */
    public static function sortAssocArrayByValue($array, $orderBy, $keySort)
    {
        $sortOrder = SORT_ASC;
        if ($orderBy == 'desc') {
            $sortOrder = SORT_DESC;
        }
        $sort = [];
        foreach ($array as $key => $part) {
            $sort[$key] = $part[$keySort];
        }
        array_multisort($sort, $sortOrder, SORT_NUMERIC, $array);
        return $array;
    }
}