<?php

namespace Modules\Frontend\Controllers;

use Illuminate\Support\Facades\Session;
use Modules\Backend\Models\UserExtend;
use Modules\Frontend\Classes\Captcha;
use Modules\Frontend\Facades\CheckOutCouponFacades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Brand;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Coupon;
use Modules\Frontend\Classes\Frontend;
use Modules\Frontend\Facades\CategoryFacades;
use Modules\Frontend\Facades\CheckOutFacades;
use Modules\Frontend\Facades\ProductFacades;
use Modules\Frontend\Facades\ReviewFacades;
use Modules\Frontend\Facades\UserFacades;
use Modules\Frontend\FrontendController;
use Stripe\Charge;
use Stripe\Error\ApiConnection;
use Stripe\Error\Card;
use Stripe\Stripe;
use Shipu\Themevel\Facades\Theme as STheme;

class AjaxController extends FrontendController
{

    /**
     * Ajax get category (ajax mode)
     */
    public function onCategoryAjax(Request $request)
    {
        $params = $request->all();
        $limitConfig = Config::getConfigByKeyInKeyConfigCache('category_page_size', System::PAGE_SIZE_DEFAULT);
        isset($request->limit) ? $limit = $request->limit : $limit = $limitConfig;
        $data = CategoryFacades::getListProductAjax($params, $params['id'], $limit);
        return view('partials.categoryDiv', $data);
    }

    /**
     * Get image by id
     */
    public static function getImageById($id)
    {
        $data = Brand::select('image')->where('id', $id)->first();
        if (!empty($data)) {
            return $data->image;
        }
        return '';
    }

    /**
     * Ajax get now shop by
     */
    public function onNowShopBy(Request $request)
    {
        $post = $request->all();
        $filterData = $post['filter_data'];
        $filterOptionArray = CategoryFacades::convertFilterForNowShopBy($filterData);
        if (isset($post['filter'])) {
            $post['filter'] = CategoryFacades::getFilterOptionForNowShopBy($post['filter'], $filterOptionArray);
        }
        $post['hidden_clear_all'] = System::DISABLE;
        if (empty($post['filter']) && empty($post['reviews'])
            && empty($post['price_range']) && empty($post['key'])
            && empty($post['brand'])) {
            $post['hidden_clear_all'] = System::ENABLE;
        }
        if (array_key_exists('brand', $post)) {
            $post['brand'] = self::getImageById($post['brand']);
        }
        $post['disable'] = System::DISABLE;
        return view('partials.categoryShopBy', $post);
    }

    /**
     * On reload gallery in product detail
     */
    public function onReloadGallery(Request $request)
    {
        $product = $request->all();
        $bigImage = $product['variant_image'];
        $gallery = [];
        if (!empty($product['variant_gallery'])) {
            $gallery = explode(System::SEPARATE, $product['variant_gallery']);
        }
        $rs = [];
        $rs['bigImage'] = $bigImage;
        $rs['gallery'] = $gallery;
        return view('partials.ajaxGallery', $rs);
    }

    /**
     * CART: Ajax reload drop down
     */
    public function onReloadCart(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);
        $totalPrice = 0;
        if (!empty($post)) {
            foreach ($post as $row) {
                $totalPrice += $row['qty']*$row['price'];
            }
        }
        $data['cart'] = $post;
        $data['totalPrice'] = $totalPrice;
        return view('partials.cartDropDown', $data);
    }

    /**
     * CART: load ajax cart
     */
    public function onAjaxCart(Request $request)
    {
        $post = $request->all();
        $data = Frontend::convertCart($post);
        return view('partials.cart', $data);
    }

    /**
     * CHECKOUT
     */
    /**
     * load ajax cart on page checkout
     */
    public function onAjaxCartCheckout(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);
        $data = Frontend::convertCart($post);
        return view('partials.checkoutCart', $data);
    }

    /**
     * Check coupon ajax
     */
    public function onCheckCoupon(Request $request)
    {
        $post = $request->all();
        $coupon = $post['coupon'];
        $couponData = Coupon::where('code', $coupon)->first();
        $loggedIn = Auth::getUser();
        if (!empty($couponData)) {
            $discount = CheckOutCouponFacades::
                calculateDiscountPrice($couponData, $post['totalPrice'], $post['cart'], $loggedIn);
        } else {
            $discount =  [
                'rs' => false,
                'msg' => STheme::lang('lang.error_coupon.not_exists'),
                'discount_price' => 0
            ];
        }
        return response()->json($discount);
    }

    /**
     * Checkout stripe
     */
    public function onCheckOutStripe(Request $request)
    {
        $post = $request->all();
        $token = $post['tokenVar'];
        $params = $post['params'];
        $totalPriceByCent = $params['total'] * 100;
        $success = 0;
        try {
            Stripe::setApiKey(Config::getConfigByKeyInKeyConfigCache('stripe_secret', ''));
            $charge = Charge::create(array(
                'amount' => $totalPriceByCent, // Amount in cents!
                'currency' => strtolower($params['currency_code']),
                'source' => $token,
                'description' => 'Checkout by stripe'
            ));
            $success = System::SUCCESS;
            //save order then send email
            $orderId = CheckOutFacades::saveOrder($params);
            CheckOutFacades::changeStatusPaymentToPaid($orderId);
            Session::flash(System::FLASH_SUCCESS, STheme::lang('lang.msg.save_order_success'));
            return response()->json(System::SUCCESS);
        } catch (ApiConnection $e) {
            $stripeError = STheme::lang('lang.msg.network_error');
        } catch (Card $e) {
            // Card was declined.
            $eJson = $e->getJsonBody();
            $error = $eJson['error'];
            $stripeError = $error['message'];
        }
        if ($success != 1) {
            $this->checkout_error = $stripeError;
            return response()->json($stripeError);
        }
    }

    /**
     * Paypal success
     */
    public function onPaypalSuccess(Request $request)
    {
        $token = $request->token;
        $tokenArray = explode('_', $token);
        $orderId = $tokenArray[1];
        CheckOutFacades::changeStatusPaymentToPaid($orderId);
        return response()->json(['rs'=>System::SUCCESS, 'msg'=>'']);
    }

    /**
     * On save order
     */
    public function onSaveOrder(Request $request)
    {
        $post = $request->all();
        $orderId = CheckOutFacades::saveOrder($post);
        if (!empty($orderId)) {
            Session::flash(System::FLASH_SUCCESS, STheme::lang('lang.msg.save_order_success'));
        }
        return response()->json($orderId);
    }


    /**
     * END CHECKOUT
     */

    /**
     * USER
     */

    /**
     * ajax register
     */
    public function onModalLogin(Request $request)
    {
        return view('partials.modalLogin');
    }

    public function onModalAddress(Request $request)
    {
        $data['action'] = $request->action;
        return view('partials.modalAddress', $data);
    }

    public function onRegister(Request $request)
    {
        $post = $request->all();
        $rs = UserFacades::doRegister($post);
        return response()->json($rs);
    }

    /**
     * ajax login
     */
    public function onLogin(Request $request)
    {
        $post = $request->all();
        $rs = UserFacades::doLogin($post);
        return response()->json($rs);
    }

    /**
     * ajax forgot password, send email to reset password
     */
    public function onForgotPassword(Request $request)
    {
        $post = $request->all();
        $rs = UserFacades::resetPassword($post);
        return response()->json($rs);
    }

    /**
     * Change password
     */
    public function onChangePassword(Request $request)
    {
        $post = $request->all();
        $rs = UserFacades::changePassword($post);
        return response()->json($rs);
    }

    /**
     * Save address
     */
    public function onSaveAddress(Request $request)
    {
        $post = $request->all();
        $rs = UserFacades::saveAddress($post);
        if ($rs['rs'] == System::SUCCESS) {
            Session::flash('notify_flash_msg', $rs['msg']);
        }
        return response()->json($rs);
    }

    /**
     * Validate form address in checkout when user not login
     */
    public function onValidateAddressNotLogin(Request $request)
    {
        $post = $request->all();
        $rs = UserFacades::validateAddressNotLogin($post['form_address_not_login_in']);
        return response()->json($rs);
    }

    /**
     * Edit address
     */
    public function onEditAddress(Request $request)
    {
        $id = $request->id;
        $data['action'] = $request->action;
        $data['address'] = UserExtend::where('id', $id)->first();
        return view('partials.modalAddress', $data);
    }

    /**
     * Delete address
     */
    public static function onDeleteAddress(Request $request)
    {
        try {
            $id = $request->id;
            UserExtend::where('id', $id)->delete();
            Session::flash('notify_flash_msg', STheme::lang('lang.msg.delete_address_success'));
            $rs = ['rs'=>System::SUCCESS, 'msg'=>''];
        } catch (\Exception $e) {
            $rs = ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
        return response()->json($rs);
    }

    /**
     * END USER
     */


    /**
     * Modal review in product detail
     */
    public function onModalReview(Request $request)
    {
        $data['productId'] = $request->productId;
        return view('partials.modalReview', $data);
    }

    /**
     * Generate captcha
     */
    public function onCaptcha(Request $request)
    {
        $captcha = new Captcha();
        $captchaData = $captcha->getAndShowImage([]);
        return response()->json($captchaData);
    }

    /**
     * Submit review
     */
    public function onSubmitReview(Request $request)
    {

        $post = $request->all();
        $loggedIn = Auth::guard('users')->user();
        $rs = ReviewFacades::addReview($post['formReview'], $loggedIn);
        Session::flash('notify_flash_msg', $rs['msg'][0]);
        return response()->json($rs);
    }

    /**
     * Get review by page
     */
    public function onReviewPage(Request $request)
    {
        $page = $request->page;
        $productId = $request->productId;
        $data['review'] = ProductFacades::getProductReview($productId, $page);
        return view('partials.reviewDisplay', $data);
    }

    /**
     * search
     */
    public function onSearchValidate(Request $request)
    {
        $formData = $request->formData;
        $msgValidate = [];
        $rule = [
            'key' => 'required|min:2',
        ];
        $validateRs = Frontend::validateForm($formData, $rule, $msgValidate);
        return response()->json($validateRs);
    }

    /**
     * Contact us
     */
    public function onContactUs(Request $request)
    {
        $rs = Frontend::contactUs($request->all());
        return response()->json($rs);
    }

}