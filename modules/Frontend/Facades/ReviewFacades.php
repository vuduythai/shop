<?php

namespace Modules\Frontend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\Review;
use Modules\Frontend\Classes\Frontend;
use Shipu\Themevel\Facades\Theme as STheme;
use Illuminate\Support\Facades\File;

class ReviewFacades extends Model
{
    /**
     * Add review Data
     */
    public static function addReviewData($data, $loggedIn)
    {
        $productReview = new Review();
        $productReview->customer_id  = 0;
        if (!empty($loggedIn)) {
            $productReview->customer_id = $loggedIn->id;
        }
        $productReview->product_id = $data['product_id'];
        $productReview->author = $data['author'];
        $productReview->content = $data['content'];
        $productReview->rate = !empty($data['rate']) ? $data['rate'] : 1;
        $approveAutomatic = Config::getConfigByKeyInKeyConfigCache('review_approve_automatic', System::NO);
        $productReview->status = $approveAutomatic == System::YES ? System::STATUS_ACTIVE : System::STATUS_UNACTIVE;
        $productReview->save();
    }

    /**
     * Add num count review
     */
    public static function addNumCountReview($productId)
    {
        $product = Product::find($productId);
        $numReviewCount = $product->review_count;
        Product::where('id', $productId)->update(['review_count'=>$numReviewCount+1]);
    }

    /**
     * Delete file in folder captcha
     */
    public static function deleteFileCaptcha()
    {
        $directory = base_path().'/public'.System::FOLDER_IMAGE.'captcha';
        $files = File::files($directory);
        if (!empty($files)) {
            foreach ($files as $file) {
                File::delete($directory.'/'.$file->getFileName());
            }
        }
    }

    /**
     * Do add Review data
     */
    public static function doAddReviewData($data, $loggedIn)
    {
        DB::beginTransaction();
        try {
            self::addReviewData($data, $loggedIn);
            self::addNumCountReview($data['product_id']);
            DB::commit();
            Cache::flush();
            self::deleteFileCaptcha();
            return ['rs'=>System::SUCCESS, 'msg'=>[STheme::lang('lang.msg.create_review_success')]];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['rs'=>System::FAIL, 'msg'=>[$e->getMessage()]];
        }
    }

    /**
     * Add review
     */
    public static function addReview($data, $loggedIn)
    {
        $msgValidate = [];
        $rule = [
            'author' => 'required',
            'content' => 'required',
            'captcha' => 'required'
        ];
        $validateRs = Frontend::validateForm($data, $rule, $msgValidate);
        if ($validateRs['rs'] == System::FAIL) {
            return $validateRs;
        } else {
            return self::doAddReviewData($data, $loggedIn);
        }
    }
}