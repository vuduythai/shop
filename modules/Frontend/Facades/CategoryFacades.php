<?php

namespace Modules\Frontend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Models\Attribute;
use Modules\Backend\Models\AttributeProperty;
use Modules\Backend\Models\Brand;
use Modules\Backend\Models\Category;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Label;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\ProductToCategory;
use Modules\Backend\Models\ProductToProperty;
use Modules\Frontend\Classes\Frontend;
use Modules\Frontend\Classes\Price;

class CategoryFacades extends Model
{

    const LOAD_NORMAL = 0;
    const LOAD_AJAX = 1;

    /**
     * Convert attribute then count product per property
     */
    public static function convertProperty($data)
    {
        $rs = [];
        if (!empty($data)) {
            $attribute = [];
            //group by attribute_id
            foreach ($data as $row) {
                $attribute[$row->attribute_id][] = $row;
            }
            $property = [];
            //group by property_id
            $arrayProductCount = [];// count product per property_id
            foreach ($attribute as $key => $value) {
                foreach ($value as $p) {
                    $property[$key][$p->property_id] = $p;
                    $arrayProductCount[$p->property_id][] = $p->product_id;//assign property to count
                }
            }
            $arrayProductCountUnique = [];
            //remove duplicate product
            foreach ($arrayProductCount as $key => $value) {
                $arrayProductCountUnique[$key] = count(array_unique($value));
            }
            foreach ($property as $key => $value) {
                foreach ($value as $p) {
                    $propertyId = $p->property_id;
                    $p->product_count = array_key_exists($propertyId, $arrayProductCountUnique) ?
                        $arrayProductCountUnique[$propertyId] : 0;//count products
                    $rs[$p->attribute_id]['attribute'] = ['name'=>$p->attribute_name];
                    $rs[$p->attribute_id]['property'][] = $p;
                }
            }
        }
        return $rs;
    }

    /**
     * Get property for category
     */
    public static function getPropertyForCategory($productArray)
    {
        // get product id array to get property id
        $productIdArray = [];
        foreach ($productArray as $row) {
            $productIdArray[] = $row->id;
        };
        //get property based on $productIdArray
        $tableAttribute = with(new Attribute())->getTable();
        $tableAttributeProperty = with(new AttributeProperty())->getTable();
        $tableProductToProperty = with(new ProductToProperty())->getTable();
        $fieldArray = [
            'a.name AS attribute_name', 'a.id AS attribute_id', 'ap.name AS property_name', 'ap.id AS property_id',
            'ap.value AS property_value', 'ap.type AS property_type', 'pp.product_id AS product_id'
        ];
        $data = DB::table($tableAttribute.' AS a')->select($fieldArray)
            ->leftJoin($tableAttributeProperty.' AS ap', 'ap.attribute_id', '=', 'a.id')
            ->leftJoin($tableProductToProperty.' AS pp', 'pp.property_id', '=', 'ap.id')
            ->where('a.is_filter', System::ENABLE)
            ->whereIn('pp.product_id', $productIdArray)
            ->get()
            ->toArray();
        $data = self::convertProperty($data);
        $data = Functions::objectToArray($data);
        return $data;
    }


    /**
     * Get min max price
     */
    public static function getPriceMinMax($categoryId)
    {
        $productTable = with(new Product())->getTable();
        $productToCategoryTable = with(new ProductToCategory())->getTable();
        $query = DB::table($productTable.' AS p')
            ->leftJoin($productToCategoryTable.' AS pc', 'p.id', '=', 'pc.product_id')
            ->where('pc.category_id', $categoryId);//filter by category
        $dataSelect = ['p.price', 'p.price_promotion', 'p.price_promo_from', 'p.price_promo_to', 'p.tax_class_id'];
        $data = $query->select($dataSelect)
            ->distinct()
            ->get()
            ->toArray();
        $data = Price::addFinalPriceForProductObject($data);
        $rs['min'] = 0;
        $rs['max'] = 0;
        if (!empty($data)) {
            $priceArray = [];
            foreach ($data as $row) {
                $priceArray[] = $row->final_price;
            }
            $rs['min'] = min($priceArray);
            $rs['max'] = max($priceArray);
        }
        return $rs;
    }

    /**
     * Count page
     */
    public static function countPage($totalPages, $currentPage, $limitPage)
    {
        $remainPage = $limitPage - floor($limitPage/2);
        $plusPage = $limitPage - $remainPage;
        if ($totalPages <= $limitPage) {
            // less than $limitPage total pages so show all
            $startPage = 1;
            $endPage = $totalPages;
        } else {
            // more than $limitPage total pages so calculate start and end pages
            if ($currentPage <= $remainPage) {
                $startPage = 1;
                $endPage = $limitPage;
            } elseif ($currentPage + $plusPage >= $totalPages) {
                $startPage = $totalPages - ($limitPage - 1);
                $endPage = $totalPages;
            } else {
                $startPage = $currentPage - floor($limitPage / 2);
                $endPage = $currentPage + $plusPage;
            }
        }
        $pages = [];
        for ($i = $startPage; $i<$endPage + 1; $i++) {
            array_push($pages, $i);
        }
        return ['pages'=>$pages, 'currentPage' => $currentPage, 'totalPages' => $totalPages];
    }

    /**
     * pagination array
     */
    public static function arrayPagination($data, $limit)
    {
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);
        // Define how many items we want to be visible in each page
        $perPage = $limit;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        return $currentPageItems;
    }

    /**
     * Get review point
     */
    public static function getReviewPoint($data)
    {
        $reviewArray = [];
        foreach ($data as $row) {
            $reviewArray[] = $row->review_point;
        }
        $reviewCount = array_count_values($reviewArray);
        ksort($reviewCount);//sort by key
        $rs = [];
        foreach ($reviewCount as $key => $value) {
            if ($key != 0) {
                $rs[] = [
                    'point' => $key,
                    'count' => $value
                ];
            }
        }
        return $rs;
    }

    /**
     * Keep property that chosen in filter layer navigation
     */
    public static function keepPropertyChosen($attribute, $propertyChosen)
    {
        $rs = [];
        foreach ($attribute as $row) {
            $attribute = $row['attribute'];
            $propertyArray = $row['property'];
            foreach ($row['property'] as $o) {
                if (in_array($o['property_id'], $propertyChosen)) {
                    $propertyArray = [];//empty first
                    $propertyArray[] = $o;//then assign property that chosen
                }
            }
            $rs[] = ['attribute'=>$attribute, 'property'=>$propertyArray];
        }
        return $rs;
    }

    /**
     * Get brand by product id array
     */
    public static function getBrandByProductIdArray($productIdArray)
    {
        $brand = Brand::select('id', 'image')->get()->toArray();
        $brandIdByProduct = [];
        foreach ($productIdArray as $row) {
            $brandIdByProduct[] = $row->brand_id;
        }
        $rs = [];
        if (!empty($brandIdByProduct)) {
            foreach ($brand as $row) {
                if (in_array($row['id'], $brandIdByProduct)) {
                    $rs[] = $row;
                }
            }
        }
        return $rs;
    }

    /**
     * Get constant data
     */
    public static function getConstantData()
    {
        $data = [
            'enable' => System::ENABLE,
            'disable' => System::DISABLE,
            'property_type_text' => Attribute::TYPE_TEXT,
            'property_type_color' => Attribute::TYPE_COLOR,
            'load_normal' => self::LOAD_NORMAL,
            'load_ajax' => self::LOAD_AJAX,
            'yes' => System::YES,
            'label_type_text_on_image' => Label::TYPE_TEXT_ON_IMAGE
        ];
        return $data;
    }

    /**
     * Get category name
     */
    public static function getCategoryData($id)
    {
        $data = Category::select('name', 'seo_title', 'seo_keyword', 'seo_description')
            ->where('id', $id)
            ->first();
        return $data;
    }

    /**
     * Filter by price range for final price
     */
    public static function filterPriceRange($product, $priceRange)
    {
        $priceMinFilter = $priceRange[0];
        $priceMaxFilter = $priceRange[1];
        $rs = [];
        foreach ($product as $row) {
            if ($row->final_price >= $priceMinFilter && $row->final_price <= $priceMaxFilter) {
                $rs[] = $row;
            }
        }
        return $rs;
    }

    /**
     * Order by final_price
     */
    public static function orderByFinalPrice($product, $orderBy)
    {
        $product = Functions::objectToArray($product);//convert to array
        $product = Functions::sortAssocArrayByValue($product, $orderBy, 'final_price');
        $rs = [];
        foreach ($product as $row) {//convert return object
            $rs[] = Functions::convertArrayToObject($row);
        }
        return $rs;
    }

    /**
     * Get list product cache
     */
    public static function getListProductCache($params, $id, $limit)
    {
        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }
        $filterArray = [];
        if (isset($params['filter'])) {
            if ($params['filter'] != '') {
                $filterArray = explode('_', $params['filter']);
            }
        }
        isset($params['sort_by']) ? $sortBy = explode('-', $params['sort_by']) : $sortBy = ['price', 'asc'];
        isset($params['key']) ? $key = $params['key'] : $key = '';
        isset($params['page']) ? $page = $params['page'] : $page = 1;
        $productTable = with(new Product())->getTable();
        $productToCategoryTable = with(new ProductToCategory())->getTable();
        $productToPropertyTable = with(new ProductToProperty())->getTable();
        $query = DB::table($productTable.' AS p')
            ->leftJoin($productToCategoryTable.' AS pc', 'p.id', '=', 'pc.product_id')
            ->where('pc.category_id', $id);//filter by category

        //filter product by filter option id
        if (!empty($filterArray)) {
            //just join when filter => avoid duplicate record
            $query->leftJoin($productToPropertyTable.' AS pp', 'p.id', '=', 'pp.product_id');
            if (count($filterArray) == 1) {
                $query->where('pp.property_id', $filterArray[0]);
            } else { //> 1 filter option, GROUP BY + HAVING COUNT
                $tablePrefix = DB::getTablePrefix();
                $query->whereIn('pp.property_id', $filterArray)
                    ->groupBy('p.id')
                    ->havingRaw('COUNT('.$tablePrefix.'pp.property_id) >= '.count($filterArray));
            }
        }

        if ($key != '') {
            $query->where('p.name', 'like', '%'.$params['key'].'%');
        }

        // filter by review
        if (isset($params['reviews'])) {
            $reviewPoint = $params['reviews'];
            $query->where('p.review_point', $reviewPoint);
        }

        // filter by brand
        if (isset($params['brand'])) {
            $brandId = $params['brand'];
            $query->where('p.brand_id', $brandId);
        }

        //get all product id for filter and price range
        //not use select(*) => Syntax error or access violation: 1055 when group by
        $arrayField = [
            'p.id', 'p.price', 'p.name', 'p.qty', 'p.qty_order', 'p.product_type',
            'p.is_has_option', 'p.image', 'p.slug', 'p.tax_class_id', 'p.review_point',
            'p.weight', 'p.weight_id', 'p.product_label', 'p.brand_id', 'p.is_in_stock',
            'p.price_promotion', 'p.price_promo_from', 'p.price_promo_to'
        ];
        $product = $query->select($arrayField)
            ->distinct()
            ->get()
            ->toArray();

        $brand = self::getBrandByProductIdArray($product);
        $minMaxPrice = self::getPriceMinMax($id);
        $reviews = self::getReviewPoint($product);
        if (!isset($params['price_range'])) {
            $priceRange = array_values($minMaxPrice);
        } else {
            $priceRange = explode('-', $params['price_range']);
        }

        $attribute = self::getPropertyForCategory($product);
        if (!empty($filterArray)) {
            $attribute = self::keepPropertyChosen($attribute, $filterArray);
        }

        $product = Price::addFinalPriceForProductObject($product);
        // filter by price range after add final_price for product in product list
        if (isset($params['price_range'])) {
            $priceRange = explode('-', $params['price_range']);
            $product = self::filterPriceRange($product, $priceRange);
        }

        if (!empty($sortBy)) {
            $orderBy = $sortBy[1];
        } else {
            $orderBy = 'asc';
        }
        $product = self::orderByFinalPrice($product, $orderBy);

        $allRecord = count($product);
        $totalPage = ceil($allRecord/$limit);
        $pages = self::countPage($totalPage, $page, $limit);
        $product = self::arrayPagination($product, $limit);



        $countProductCurrentPage = count($product);
        $categoryData = self::getCategoryData($id);
        $breadCrumb = [
            'type' => System::ROUTES_TYPE_CATEGORY,
            'data' => Frontend::getBreadCrumb($id),
            'name' => isset($categoryData->name) ? $categoryData->name : ''
        ];
        $seo = [
            'seo_title' => $categoryData->seo_title ? $categoryData->seo_title : '',
            'seo_keyword' => $categoryData->seo_keyword ? $categoryData->seo_keyword : '',
            'seo_description' => $categoryData->seo_description ? $categoryData->seo_description : ''
        ];
        $rs = [
            'filter' => $attribute,
            'products' => $product,
            'minMaxPrice' => $minMaxPrice,
            'priceRange' => $priceRange,
            'pages' => $pages,
            'sortByString' => implode('-', $sortBy),
            'filterChecked' => $filterArray,
            'count' => $countProductCurrentPage,
            'key' => $key,
            'breadcrumbArray' => $breadCrumb,
            'reviews' => $reviews,
            'allLabel' => Label::getAllProductLabel(),
            'brand' => $brand,
            'firstClassArray' => Functions::generateArrayForFirstClass(1, 100, 3),
            'config' => Config::getConfigByKeyConfig(),
            'const' => self::getConstantData(),
            'category_id' => $id,
            'categoryName' => isset($categoryData->name) ? $categoryData->name : '',
            'seo' => $seo
        ];
        return $rs;
    }

    /**
     * Get list product cache
     */
    public static function getListProductData($params, $id, $limit)
    {
        $cacheKey = 'category_'.$id.'_';
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $cacheKey .= $key.'_'.$value.'_';
            }
        }
        $rs = AppModel::returnCacheData($cacheKey, function () use ($params, $id, $limit) {
            return self::getListProductCache($params, $id, $limit);
        });
        $rs['products'] = Frontend::checkProductCanBuy($rs['products']);
        $rs['products'] = Functions::objectToArray($rs['products']);
        return $rs;
    }

    /**
     * Get list product - category
     */
    public static function getListProduct($request, $id, $limit)
    {
        $params = $request->all();
        $rs = self::getListProductData($params, $id, $limit);
        return $rs;
    }

    /**
     * Get list product - category ajax
     */
    public static function getListProductAjax($params, $id, $limit)
    {
        $rs = self::getListProductData($params, $id, $limit);
        return $rs;
    }

    /**
     * Convert filter for now shopping by
     */
    public static function convertFilterForNowShopBy($filterData)
    {
        $propertyArray = [];
        if (!empty($filterData)) {
            $attributeArray = json_decode($filterData, true);
            foreach ($attributeArray as $row) {
                foreach ($row['property'] as $o) {
                    $propertyArray[$o['property_id']] = [
                        'attributeName' => $row['attribute']['name'],
                        'propertyName' => $o['property_name'],
                        'propertyId' => $o['property_id']
                    ];
                }
            }
        }
        return $propertyArray;
    }

    /**
     * Get filter for now shop by
     */
    public static function getFilterOptionForNowShopBy($filter, $propertyArray)
    {
        if (!empty($filter)) {
            $propertyChosen = explode('_', $filter);
            $filterConvert = [];
            foreach ($propertyChosen as $row) {
                if (array_key_exists($row, $propertyArray)) {
                    $filterConvert[] = [
                        'attributeName' => $propertyArray[$row]['attributeName'],
                        'propertyName' => $propertyArray[$row]['propertyName'],
                        'propertyId' => $propertyArray[$row]['propertyId']
                    ];
                }
            }
            return $filterConvert;
        }
        return [];
    }
}