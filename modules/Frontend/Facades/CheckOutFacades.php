<?php
/**
 * Handle checkout in frontend
 */
namespace Modules\Frontend\Facades;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Backend\Models\Currency;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Order;
use Modules\Backend\Models\OrderOption;
use Modules\Backend\Models\OrderProduct;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\CouponHistory;
use Modules\Backend\Models\Payment;
use Modules\Backend\Models\ProductVariant;
use Modules\Backend\Models\Shipping;
use Modules\Backend\Models\UserExtend;
use Illuminate\Support\Facades\DB;
use Shipu\Themevel\Facades\Theme as STheme;

class CheckOutFacades extends Model
{
    const PAYPAL_SANDBOX = 0;
    const PAYPAL_PRODUCTION = 1;
    const IS_LOGIN = 1;
    const NOT_LOGIN = 0;
    const CASH_ON_DELIVERY = 'cod';//use slug
    const PAYPAL = 'paypal';//use slug
    const STRIPE = 'stripe';//use slug

    /**
     * Constant for js
     */
    public static function constantData()
    {
        $data = [
            'ship_type_price' => Shipping::TYPE_PRICE,
            'ship_type_geo' => Shipping::TYPE_GEO,
            'ship_type_weight_based' => Shipping::TYPE_WEIGHT_BASED,
            'ship_type_per_item' => Shipping::TYPE_PER_ITEM,
            'ship_type_geo_weight_based' => Shipping::TYPE_GEO_WEIGHT_BASED,
            'weight_type_fixed' => Shipping::WEIGHT_TYPE_FIXED,
            'weight_type_rate' => Shipping::WEIGHT_TYPE_RATE,
            'enable' => System::ENABLE,
            'disable' => System::DISABLE,
            'rs_fail' => System::FAIL,
            'rs_success' => System::SUCCESS,
            'cod_method' => self::CASH_ON_DELIVERY,
            'paypal_method' => self::PAYPAL,
            'stripe_method' => self::STRIPE,
            'symbol_position_before' => Currency::POSITION_BEFORE,
            'symbol_position_after' => Currency::POSITION_AFTER
        ];
        return $data;
    }

    /**
     * Return payment method name
     */
    public static function getPaymentName($slug)
    {
        $data = Payment::select('name')->where('slug', $slug)->first();
        if (!empty($data)) {
            return $data->name;
        }
        return '';
    }

    /**
     * address not login
     * Get information of address from form
     */
    public static function addressNotLogin($arrayName, $formAddress)
    {
        $data = [];
        if (empty($formAddress['use_same_address_not_login'])) {//shipping address != billing address
            foreach ($arrayName as $row) {
                $data['billing_'.$row] = $formAddress['billing_'.$row];
                $data['shipping_'.$row] = $formAddress['shipping_'.$row];
            }
        } else {//shipping address == billing address
            foreach ($arrayName as $row) {
                $data['billing_'.$row] = $formAddress['billing_'.$row];
                $data['shipping_'.$row] = $formAddress['billing_'.$row];
            }
        }
        return $data;
    }

    /**
     * Address when login
     *  Get information of address from database
     */
    public static function addressLogin($post, $arrayName)
    {
        $addressBilling = $post['address_billing'];
        $addressShipping = $post['address_shipping'];
        $address = UserExtend::whereIn('id', [$addressBilling, $addressShipping])->get()->toArray();
        $rs = [];
        foreach ($address as $row) {
            $rs[$row['id']] = $row;
        }
        $billing = $rs[$addressBilling];
        $shipping = $rs[$addressShipping];
        if ($addressBilling == $addressShipping) {//if address billing and shipping is the same
            $billing = $address[0];
            $shipping = $address[0];
        }
        $data = [];
        foreach ($arrayName as $row) {
            $data['billing_'.$row] = $billing[$row];
            $data['shipping_'.$row] = $shipping[$row];
        }
        $data['user_id'] = $addressBilling;
        return $data;
    }

    /**
     * Change status payment to paid
     */
    public static function changeStatusPaymentToPaid($orderId)
    {
        $data = ['payment_status'=>Order::PAYMENT_STATUS_PAID];
        Order::where('id', $orderId)->update($data);
    }

    public static function billingAndShippingAddress($post)
    {
        $arrayName = ['first_name', 'last_name', 'address', 'email', 'phone'];
        if ($post['user_id'] == 0) {//not logged in
            $address = self::addressNotLogin($arrayName, $post['form_address_not_login_in']);
        } else {//logged in
            $address = self::addressLogin($post, $arrayName);
        }
        return $address;
    }

    public static function saveProduct($orderId, $product)
    {
        $orderProduct = [];
        $orderOptionData = [];
        foreach ($product as $key => $row) {
            $productIdArray = explode('-', $key);
            if (count($productIdArray) == 1) {
                $productId = $productIdArray[0];
                $variantId = 0;
            } else {
                $productId = $productIdArray[0];
                $variantId = $productIdArray[1];
            }
            $orderProduct[] = [
                'order_id' => $orderId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => $row['name'],
                'qty' => $row['qty'],
                'price_after_tax' => $row['price'],
                'total' => $row['qty'] * $row['price'],
                'weight' => $row['weight'],
                'weight_id' => $row['weight_id']
            ];
            //insert #_order_option
            if (!empty($row['order_option'])) {
                $orderOptionArray = json_decode($row['order_option'], true);
                foreach ($orderOptionArray as $o) {
                    $o['order_id'] = $orderId;
                    $orderOptionData[] = $o;
                }
            }
        }
        if (!empty($orderOptionData)) {
            OrderOption::insert($orderOptionData);
        }
        OrderProduct::insert($orderProduct);
    }

    /**
     * Save order quantity
     */
    public static function saveOrderQuantity($product)
    {
        //add product qty
        foreach ($product as $key => $value) {
            $productObj = Product::where('id', $value['id'])->first();
            $qtyOrder =  $productObj->qty_order + $value['qty'];
            $productObj::where('id', $value['id'])->update(['qty_order'=>$qtyOrder]);
        }
        //add variant qty
        foreach ($product as $key => $value) {
            $productIdArray = explode('-', $key);
            if (count($productIdArray) == 2) {
                $variantId = $productIdArray[1];
                $variant = ProductVariant::where('id', $variantId)->first();
                $qtyOrder = $variant->qty_order + $value['qty'];
                $variant::where('id', $variantId)->update(['qty_order'=>$qtyOrder]);
            }
        }
    }

    /**
     * Save order data
     */
    public static function saveOrderData($post)
    {
        $orderData = [
            'user_id' => $post['user_id'],
            'total' => $post['total'],
            'comment' => isset($post['comment']) ? $post['comment'] : '',
            'shipping_rule_id' => $post['shipping_rule_id'],
            'shipping_cost' => $post['shipping_cost'],
            'order_status_id' => 1,
            'payment_status' => Order::PAYMENT_STATUS_NOT_PAID,
            'payment_method' => $post['payment_method'],
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
        $address = self::billingAndShippingAddress($post);
        $orderData = array_merge($orderData, $address);
        $orderId = Order::insertGetId($orderData);
        $product = $post['cart'];
        self::saveProduct($orderId, $product);
        self::saveOrderQuantity($product);
        return $orderId;
    }

    /**
     * Calculate total price per item in cart
     */
    public static function calculatePricePerItemInCart($cart)
    {
        $rs = [];
        if (!empty($cart)) {
            foreach ($cart as $row) {
                $row['total_price_per_item'] = $row['qty'] * $row['price'];
                $rs[] = $row;
            }
        }
        return $rs;
    }

    /**
     * Send mail order
     */
    public static function sendOrderMail($orderId, $post)
    {
        if (Auth::guard('users')->check()) {//for loggedIn user
            $user = Auth::guard('users')->user();
            $arrayEmailUnique = [$user->email];
            $userId = $user->id;
            $userExtendData = UserExtend::find($userId);
            if (!empty($userExtendData)) {
                $name = $userExtendData->first_name.' '.$userExtendData->last_name;
            } else {
                $name = 'John Doe';
            }
        } else {//for guest
            $formData = $post['form_address_not_login_in'];
            if (empty($formData['shipping_email'])) {
                $emailArray = [$formData['billing_email']];
            } else {
                $emailArray = [$formData['billing_email'], $formData['shipping_email']];
            }
            $arrayEmailUnique = array_unique($emailArray);
            $name = $formData['billing_first_name'].' '.$formData['billing_last_name'];
        }
        $post['orderId'] = $orderId;
        $post['cart'] = self::calculatePricePerItemInCart($post['cart']);
        foreach ($arrayEmailUnique as $email) {
            $params = [
                'email' => $email,
                'name' => $name,
                'subject' => STheme::lang('lang.subject.order_success'),
                'data' => $post,
                'template' => 'mails.orderSuccess'
            ];
            System::sendMail($params);
        }
    }

    /**
     * Save coupon id history
     */
    public static function saveCouponIdHistory($orderId, $post)
    {
        $couponHistory = new CouponHistory();
        $couponHistory->order_id = $orderId;
        $couponHistory->coupon_id = $post['coupon_id'];
        $couponHistory->customer_id = $post['user_id'];
        $couponHistory->total = $post['coupon_total'];
        $couponHistory->save();
    }

    /**
     * Save order
     */
    public static function saveOrder($post)
    {
        DB::beginTransaction();
        try {
            $orderId = self::saveOrderData($post);
            if (isset($post['coupon_id']) && $post['coupon_id'] != 0) {
                self::saveCouponIdHistory($orderId, $post);
            }
            DB::commit();
            //send mail
            self::sendOrderMail($orderId, $post);
            Cache::flush();
            return $orderId;
        } catch (\Exception $e) {
            DB::rollBack();
            return System::FAIL;
        }
    }

    /**
     * Get currency default
     */
    public static function getCurrencyDefault($id)
    {
        return Currency::find($id);
    }

    /**
     * Get all ship
     */
    public static function getAllShip()
    {
        return Shipping::where('status', System::ENABLE)->get();
    }

    /**
     * Get all payment
     */
    public static function getAllPayment()
    {
        return Payment::where('status', System::ENABLE)->get();
    }

    /**
     * Get user extend data
     */
    public static function getUserExtendData($id)
    {
        return UserExtend::where('user_id', $id)->get();
    }

}