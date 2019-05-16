<?php
namespace Modules;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Label;

class IHelpers
{

    /**
     * Generate slug
     */
    public static function genSlug($slug, $type = System::ROUTES_TYPE_PRODUCT)
    {
        $config = Config::getConfigByKeyCache('config', '');
        $isRouterOneLevel = isset($config['is_router_one_level']) ? $config['is_router_one_level'] : System::YES;
        $twoLevelRouterProduct = isset($config['two_level_router_product']) ?
            $config['two_level_router_product'] : 'product';
        $twoLevelRouterCategory = isset($config['two_level_router_category']) ?
            $config['two_level_router_category'] : 'category';
        $twoLevelRouterPage = isset($config['two_level_router_page']) ?
            $config['two_level_router_page'] : 'page';
        if ($isRouterOneLevel == System::YES) {
            $slugGenerate = URL::to('/'.$slug);
        } else {
            if ($type == System::ROUTES_TYPE_PRODUCT) {//product
                $slugGenerate = URL::to('/'.$twoLevelRouterProduct.'/'.$slug);
            } elseif ($type == System::ROUTES_TYPE_CATEGORY) {//category
                $slugGenerate = URL::to('/'.$twoLevelRouterCategory.'/'.$slug);
            } else {//page
                $slugGenerate = URL::to('/'.$twoLevelRouterPage.'/'.$slug);
            }
        }
        return $slugGenerate;
    }

    /**
     * imageDisplay
     */
    public static function imageDisplaySlir($crop, $imagePath)
    {
        $baseUrl = URL::to('/');
        $noImagePath = URL::to('/modules/backend/assets/img/no_image.jpg');
        $path = $baseUrl.'/slir/'.$crop.System::FOLDER_IMAGE.$imagePath;
        if ($imagePath == '') {
            echo $noImagePath;
        } else {
            echo $path;
        }
    }

    /**
     * Display review star
     */
    public static function displayReviewStar($rate)
    {
        $html = '';
        for ($i = 0; $i<$rate; $i++) {
            $html .= '<span class="fa fa-star checked"></span>';
        }
        if ($rate < 5) {
            $k = 5 - $rate;
            for ($y = 0; $y<$k; $y++) {
                $html .= '<span class="fa fa-star"></span>';
            }
        }
        return $html;
    }

    /**
     * Date display
     */
    public static function dateDisplay($date)
    {
        $rs = '';
        if (!empty($date)) {
            $rs = Carbon::createFromFormat('Y-m-d H:i:s', $date)->toFormattedDateString();
        }
        return $rs;
    }

    /**
     * Generate breadcrumbs
     */
    public static function generateBreadCrumbs($name, $type = System::ROUTES_TYPE_PAGE)
    {
        if ($type == System::ROUTES_TYPE_PAGE) {
            return '<li>'.$name.'</li>';
        }
    }

    /**
     * Display order id
     */
    public static function strPad($text)
    {
        return str_pad($text, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate breadcrumbs for category and product detail
     */
    public static function generateBreadCrumbsForProduct($array)
    {
        $num = count($array['data']);
        if ($array['type'] == System::ROUTES_TYPE_CATEGORY || $array['type'] == System::ROUTES_TYPE_PAGE) {
            $i = 1;//category, page
        } else {
            $i = 0;//product detail
        }
        $html = '';
        foreach ($array['data'] as $row) {
            if ($i < $num) {
                //just category has link (in case of routes two level)
                $slug = self::genSlug($row['slug'], System::ROUTES_TYPE_CATEGORY);
                $html .= '<li><a href="'.$slug.'">'.$row['name'].'</a></li>';
            }
            $i++;
        }
        $html .= '<li>'.$array['name'].'</li>';
        return $html;
    }

    /**
     * helper display image
     */
    public static function helperDisplayImage($imagePath)
    {
        $baseUrl = URL::to('/');
        $noImagePath = URL::to('/modules/backend/assets/img/no_image.jpg');
        $path = $baseUrl.System::FOLDER_IMAGE.$imagePath;
        if ($imagePath == '') {
            return $noImagePath;
        } else {
            return $path;
        }
    }

    /**
     * Display label
     */
    public static function displayLabel($productLabel, $allLabel)
    {
        $html = '';
        if (!empty($productLabel)) {
            $separate = System::SEPARATE;
            $labelArray = explode($separate, $productLabel);
            foreach ($labelArray as $label) {
                isset($allLabel[$label]) ? $labelData = $allLabel[$label] : $labelData = [];
                if (!empty($labelData)) {
                    if ($labelData['type'] == Label::TYPE_TEXT_ON_IMAGE) {
                        $html .= '<p style="'.$labelData['css_inline_text'].'">'. $labelData['text_display'] .'</p>';
                    }
                    $imageDisplay = self::helperDisplayImage($labelData['image']);
                    $html .= '<img src="'.$imageDisplay.'" style="'.$labelData['css_inline_image'].'" />';
                }
            }
        }
        return $html;
    }
}