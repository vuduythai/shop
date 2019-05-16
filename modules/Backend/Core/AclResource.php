<?php

namespace Modules\Backend\Core;

class AclResource
{
    /**
     * Route and action in acl
     */
    public static function aclSource()
    {
        $controller = [
            'product', 'category', 'theme', 'order', 'currency', 'geo', 'attribute', 'attribute_set',
            'attribute_group',  'option', 'brand', 'label', 'weight', 'length', 'customer', 'tax_class',
            'shipping', 'coupon', 'backend_user', 'role', 'order_status', 'review', 'payment', 'page',
            'block', 'language'
        ];
        $action = ['index', 'store', 'destroy'];
        $acl = [];
        foreach ($controller as $c) {
            foreach ($action as $a) {
                $acl[$c]['label'] = __('Backend.Lang::lang.controller.'.$c);
                $acl[$c]['controller'] = $c;
                $acl[$c]['resource'][] = [
                    'label' => __('Backend.Lang::lang.action.'.$a),
                    'name' => $c.'_'.$a
                ];
            }
        }
        //add more acl 'config' with action 'index' and 'store'
        $acl['config'] = [
            'label' => __('Backend.Lang::lang.controller.config'),
            'controller' => 'config',
            'resource' => [
                [
                    'label' => __('Backend.Lang::lang.action.index'),
                    'name' => 'config'.'_'.'index'
                ],
                [
                    'label' => __('Backend.Lang::lang.action.store'),
                    'name' => 'config'.'_'.'store'
                ]
            ]
        ];
        //add more acl 'setting' with action 'index'
        $acl['setting'] = [
            'label' => __('Backend.Lang::lang.controller.setting'),
            'controller' => 'setting',
            'resource' => [
                [
                    'label' => __('Backend.Lang::lang.action.index'),
                    'name' => 'setting'.'_'.'index'
                ]
            ]
        ];
        //add more resource for acl 'category' - re order category and delete category
        $acl['category']['resource'][2] = [
            'label' => __('Backend.Lang::lang.action.delete'),
            'name' => 'category-delete'.'_'.'onDeleteCategory'
        ];
        $acl['category']['resource'][3] = [
            'label' => __('Backend.Lang::lang.action.re_order_category'),
            'name' => 'category-re-order-update'.'_'.'onReOrderUpdate'
        ];

        //add more resource for acl 'order' save invoice template, change order status, change payment status
        $acl['order']['resource'][3] = [
            'label' => __('Backend.Lang::lang.action.invoice_template_save'),
            'name' => 'invoice-save-template'.'_'.'invoiceSaveTemplate'
        ];
        $acl['order']['resource'][4] = [
            'label' => __('Backend.Lang::lang.action.change_order_status'),
            'name' => 'order-change-order-status'.'_'.'onChangeOrderStatusHistory'
        ];
        $acl['order']['resource'][5] = [
            'label' => __('Backend.Lang::lang.action.change_payment_status'),
            'name' => 'order-change-payment-status'.'_'.'onChangePaymentStatus'
        ];
        //add more resource for acl 'currency'
        $acl['currency']['resource'][3] = [
            'label' => __('Backend.Lang::lang.action.convert'),
            'name' => 'currency-convert'.'_'.'onConvert'
        ];
        //add more acl 'mail' with action 'index' and 'store'
        $acl['mail'] = [
            'label' => __('Backend.Lang::lang.controller.mail'),
            'controller' => 'mail',
            'resource' => [
                [
                    'label' => __('Backend.Lang::lang.action.index'),
                    'name' => 'mail'.'_'.'index'
                ],
                [
                    'label' => __('Backend.Lang::lang.action.store'),
                    'name' => 'mail'.'_'.'store'
                ]
            ]
        ];
        return $acl;
    }

    /**
     * Get resource name
     */
    public static function getResourceName()
    {
        $rs = [];
        $permission = self::aclSource();
        foreach ($permission as $p) {
            foreach ($p['resource'] as $r) {
                $rs[] = $r['name'];
            }
        }
        return $rs;
    }

    /**
     * Get resource controller and permission
     */
    public static function convertResourceForMenu($permission)
    {
        $rs = [];
        foreach ($permission as $key => $value) {
            if (strpos($key, 'index') !== false) {
                $keyRemoveIndex = str_replace('_index', '', $key);
                $rs[$keyRemoveIndex] = $value;
            }
        }
        return $rs;
    }
}