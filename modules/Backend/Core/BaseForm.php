<?php

namespace Modules\Backend\Core;

class BaseForm
{
    /**
     * form old data
     */
    public static function formDataAssign($data, $fieldName, $assignValue)
    {
        if (isset($data->$fieldName)) {
            return $data->$fieldName;
        } else {
            return $assignValue;
        }
    }

    /**
     * Generate form field
     * $data : data of record get from database
     * $type : text, textarea, select, switch, radio, checkbox
     * $name: name of field input, select, checkbox, radio
     * $dataOption: data for field select, radio, checkbox
     * $isRequired
     * $chosen for type multipleSelect
     * $assign: default value assign for form field
     * $comment
     * $action
     */
    public static function generateFormField(
        $data,
        $controller,
        $type,
        $name,
        $dataOption = [],
        $isRequire = System::NO,
        $chosen = [],
        $assign = '',
        $comment = '',
        $action = ''
    ) {
        $class = 'form-control';
        if ($type == 'select') {
            $class = 'form-control select2';
        }
        $label = $name;
        if (preg_match('/[\[\]\']/', $name)) {
            //for example name="category[]"
            $label = substr($name, 0, -2);
        }
        $arrayFieldGeneral = [
            'name', 'slug', 'description', 'sort_order', 'status', 'value', 'type',
            'image', 'unit', 'email'
        ];
        if (in_array($name, $arrayFieldGeneral)) {
            $label = trans('Backend.Lang::lang.field.'.$label);
        } else {
            $label = trans('Backend.Lang::lang.'.$controller.'.'.$label);
        }
        $field = [
            'type' => $type,
            'name' => $name,
            'label' => $label,
            'value' => self::formDataAssign($data, $name, $assign),
            'class' => $class,
            'id' => $name,
            'data' => $dataOption,
            'chosen' => $chosen,
            'comment' => $comment,
            'action' => $action
        ];
        if ($isRequire == System::YES) {
            $field['is_required'] = System::YES;
        }
        return $field;
    }

    /**
     * Generate form
     * $data, $type, $name, $dataOption, $isRequired, $chosen, $assign, $comment, $action
     */
    public static function generateForm($data, $controller, $arrayField)
    {
        $form = [];
        foreach ($arrayField as $row) {
            $form[$row[1]] = self::generateFormField(
                $data,
                $controller,
                $row[0],//type
                $row[1],//name
                $row[2],//dataOption
                isset($row[3]) ? $row[3] : System::NO,//isRequired
                isset($row[4]) ? $row[4] : [],//chosen
                isset($row[5]) ? $row[5] : '',//assign
                isset($row[6]) ? $row[6] : '',//comment
                isset($row[7]) ? $row[7] : ''//action
            );
        }
        return $form;
    }
}