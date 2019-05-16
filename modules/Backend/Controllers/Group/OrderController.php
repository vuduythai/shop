<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Facades\OrderFacades;
use Modules\Backend\Core\Functions;
use Modules\Backend\Models\Config;
use Modules\Backend\Core\System;
use Modules\Backend\Core\Twig;

class OrderController extends BackendGroupController
{

    /**
     * Override function update
     */
    public function edit(Request $request, $id)
    {
        $data['controller'] = 'order';
        $data['id'] = $id;
        $data['invoiceUrl'] = Url('/'.config('app.admin_url').'/order/invoice/'.$id);
        $order = OrderFacades::getOrderDetail($id);
        $data['orderStatus'] = OrderFacades::getOrderStatus();
        $data['order'] = $order;
        $data['paid'] = System::PAYMENT_STATUS_PAID;
        $data['notPaid'] = System::PAYMENT_STATUS_NOT_PAID;
        $msgJs = Lang::get('Backend.Lang::lang.msg_js');
        $data['msg_js'] = json_encode($msgJs);
        $data['enable'] = System::ENABLE;
        return view('Backend.View::group.order.edit', $data);
    }

    public function onChangeOrderStatusHistory(Request $request)
    {
        $post = $request->all();
        $form = Functions::convertFormFromJs($post['form']);
        OrderFacades::createOrderStatusChange($form);
        Session::flash('msg', trans('Backend.Lang::lang.order.change_order_status_success'));
        return ['rs'=>System::SUCCESS, 'msg'=>''];
    }

    /**
     * Ajax - Change payment status
     */
    public function onChangePaymentStatus(Request $request)
    {
        $post = $request->all();
        OrderFacades::changePaymentStatus($post);
        Session::flash('msg', trans('Backend.Lang::lang.order.change_payment_status_successful'));
        return ['rs'=>System::SUCCESS, 'msg'=>''];
    }

    /**
     * Invoice
     */
    public function invoice(Request $request)
    {
        $id = $request->id;
        $order = OrderFacades::getOrderDetail($id);
        $data['order'] = $order;
        $data['created_at'] = date('Y-m-d', strtotime($order->created_at));
        $css = Config::getConfigByKey('invoice_css', '');
        $template = Config::getConfigByKey('invoice_template', '');
        return Twig::parse($css.''.$template, $data);//twig parse code from string
    }

    /**
     * template
     */
    public function template()
    {
        $data['css'] = Config::getConfigByKey('invoice_css', '');
        $data['template'] = Config::getConfigByKey('invoice_template', '');
        $data['controller'] = 'order';
        $data['action'] = 'template';
        return view('Backend.View::group.order.template', $data);
    }

    /**
     * Save Template
     */
    public function invoiceSaveTemplate(Request $request)
    {
        try {
            $data = $request->formData;
            $css = Config::where('slug', 'invoice_css')->first();
            $css->value = $data['invoice_css'];
            $css->save();
            $template = Config::where('slug', 'invoice_template')->first();
            $template->value = $data['invoice_template'];
            $template->save();
            Session::flash('msg', trans('Backend.Lang::lang.order.update_invoice_template_success'));
            $rs = ['rs'=>System::SUCCESS, 'msg'=>'', 'closeRs'=>$request->closeRs];
        } catch (\Exception $e) {
            $rs = ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
        return response()->json($rs);
    }

}