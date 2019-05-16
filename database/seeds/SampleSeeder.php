<?php

use Illuminate\Database\Seeder;
use Modules\Backend\Core\Functions;
use Illuminate\Support\Facades\DB;

class SampleSeeder extends Seeder
{
    public function run()
    {
        include('seeder_data.php');
        $seederData = new SeederData();

        //attribute group
        $name = ['name'];
        $data = [
            ['Display'],
            ['Memory'],
            ['Storage']
        ];
        $attributeGroup = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\AttributeGroup::insert($attributeGroup);

        //attribute
        $attribute = $seederData->getAttributeData();
        \Modules\Backend\Models\Attribute::insert($attribute);

        //attribute property
        $property = $seederData->getPropertyData();
        \Modules\Backend\Models\AttributeProperty::insert($property);

        //attribute set
        $name = ['name', 'attribute_json'];
        $data = [
            ['Laptop', '["1","3"]'],
            ['Mobile', '["1","4","2","5"]']
        ];
        $attributeSet = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\AttributeSet::insert($attributeSet);

        //language
        $name = ['name', 'code'];
        $data = [
            ['English', 'en'],
            ['France', 'fr']
        ];
        $language = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Language::insert($language);

        //role
        $role = $seederData->getRoleData();
        \Modules\Backend\Models\Role::insert($role);

        //brand
        $name = ['name', 'image', 'sort_order'];
        $data = [
            ['Lenovo', 'brand/1550372509lenovo.jpeg', 0],
            ['BMW', 'brand/1550372636bmw.jpeg', 0]
        ];
        $brand = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Brand::insert($brand);

        //category
        $category = $seederData->getCategoryData();
        \Modules\Backend\Models\Category::insert($category);

        //currency
        $name = ['name', 'code', 'symbol', 'symbol_position', 'value', 'created_at', 'updated_at'];
        $data = [
            ['usd', 'USD', '$', 0, 1.0000, '2019-03-10 08:47:50', '2019-03-10 08:47:50'],
            ['Canadian dolar', 'CAD', 'C$', 0, 1.3346, '2019-03-19 08:01:03', '2019-03-19 08:01:03']
        ];
        $currency = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Currency::insert($currency);

        //geo zone
        $name = ['name', 'description'];
        $data = [
            ['Area 1', null],
            ['Area 2', null],
            ['Area 3', null]
        ];
        $geo = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Geo::insert($geo);

        //label
        $name = ['name', 'text_display', 'image', 'css_inline_text', 'css_inline_image', 'type'];
        $data = [
            ['Off 12%', 'off 12%', 'label/1550216977upto-12-percent-off-icon.png', null,
                'width:60px;height:60px;position:absolute;top:0;right:16px;z-index:999', 1],
            ['Bestseller', 'Bestseller', 'label/1550216977bestseller.png', null,
                'width:60px;height:60px;position:absolute;top:0;left:16px;z-index:999', 1]
        ];
        $label = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Label::insert($label);

        //length
        $name = ['name', 'unit', 'value'];
        $data = [
            ['metre', 'm', 1.00],
            ['Centimes', 'cm', 1000.00]
        ];
        $length = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Length::insert($length);

        //weight
        $name = ['name', 'unit', 'value'];
        $data = [
            ['Kilogram', 'kg', 1.00],
            ['grams', 'gr', 1000.00]
        ];
        $weight = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Weight::insert($weight);

        //option
        $option = $seederData->getOptionData();
        \Modules\Backend\Models\Option::insert($option);

        //option value
        $optionValue = $seederData->getOptionValueData();
        \Modules\Backend\Models\OptionValue::insert($optionValue);

        //order status
        $name = ['name', 'color'];
        $data = [
            ['Pending', '#9b59b6'],
            ['Shipped', '#f39c12'],
            ['Processing', '#2ecc71'],
            ['Complete', '#3498db'],
            ['Denied', '#c0392b'],
            ['Failed', '#7f8c8d'],
            ['Refunded', '#2b3e50'],
            ['Canceled', '#2b3e50'],
            ['On hold', '#f1c40f']
        ];
        $orderStatus = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\OrderStatus::insert($orderStatus);

        //payment
        $name = ['name', 'code', 'description', 'status'];
        $data = [
            ['Cash on delivery', 'cod', 'Cash on delivery', 1],
            ['Paypal', 'paypal', null, 1],
            ['Stripe', 'stripe', null, 1]
        ];
        $payment = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Payment::insert($payment);

        //ship
        $name = ['name', 'above_price', 'geo_zone_id', 'weight_type', 'weight_based', 'cost', 'type', 'status'];
        $data = [
            ['Free Ship', 200.00, 0, 1, '0', 0.00, 1, 1],
            ['Express Ship', 100.00, 0, 1, '0', 30.00, 1, 1],
            ['Area 1', 300.00, 1, 1, '0', 12.00, 2, 1]
        ];
        $ship = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Shipping::insert($ship);

        //tax class
        $name = ['name', 'description'];
        $data = [
            ['No tax', null],
            ['Vat', null]
        ];
        $taxClass = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\TaxClass::insert($taxClass);

        //tax rate
        $name = ['name', 'type', 'geo_zone_id', 'rate'];
        $data = [
            ['Free', 1, 1, 0.00],
            ['Vat', 2, 1, 10.00]
        ];
        $taxRate = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\TaxRate::insert($taxRate);

        //tax rule
        $name = ['tax_class_id', 'tax_rate_id'];
        $data = [
            [1, 1],
            [2, 2]
        ];
        $taxRule = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\TaxRule::insert($taxRule);

        //theme
        $name = ['name', 'slug', 'description'];
        $data = [
            ['homepage slider', 'homepage-slider', null],
            ['Homepage brand', 'homepage-brand', null]
        ];
        $theme = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\Theme::insert($theme);

        //theme image
        $name = ['name', 'image', 'link', 'title', 'alt', 'description'];
        $data = [
            ['Gift', 'slider/1552272249slider3.png', null, null, null, null],
            ['hot deal', 'slider/1552272249slider4.png', null, null, null, null],
            ['Money back', 'slider/1552272249slider5.png', null, null, null, null],
            ['Sale off', 'slider/1552272249slider6.png', null, null, null, null],
            ['Banner 1', 'brand/1552271986brand-1.jpg', null, null, null, null],
            ['Banner 2', 'brand/1552271991brand-2.jpg', null, null, null, null],
            ['Banner 3', 'brand/1552271991brand-3.jpg', null, null, null, null],
            ['Banner 4', 'brand/1552271991brand-4.jpg', null, null, null, null],
            ['Banner 5', 'brand/1552271991brand-5.jpg', null, null, null, null],
            ['Banner 6', 'brand/1552271991brand-6.jpg', null, null, null, null]
        ];
        $themeImage = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\ThemeImage::insert($themeImage);

        //theme to image
        $name = ['theme_id', 'theme_image_id', 'sort_order'];
        $data = [
            [1, 1, 1],
            [1, 2, 2],
            [1, 3, 3],
            [1, 4, 4],
            [2, 5, 1],
            [2, 6, 2],
            [2, 7, 3],
            [2, 8, 4],
            [2, 9, 5],
            [2, 10, 6],
        ];
        $themeToImage = Functions::assignNameAndValueForArray($name, $data);
        \Modules\Backend\Models\ThemeToImage::insert($themeToImage);

        //product;
        $product = $seederData->getProductData();
        $product = Functions::removeRnt($product);
        \Modules\Backend\Models\Product::insert($product);

        //product to category
        $productToCategory = $seederData->getProductToCategoryData();
        \Modules\Backend\Models\ProductToCategory::insert($productToCategory);

        //product to attribute
        $productToProperty = $seederData->getProductToPropertyData();
        \Modules\Backend\Models\ProductToProperty::insert($productToProperty);

        //product to option
        $productToOption = $seederData->getProductToOptionData();
        \Modules\Backend\Models\ProductToOption::insert($productToOption);

        //product variant
        $variant = $seederData->getProductVariantData();
        \Modules\Backend\Models\ProductVariant::insert($variant);

        //$route
        $route = $seederData->getRoutesData();
        \Modules\Backend\Models\Routes::insert($route);

        $prefix = DB::getTablePrefix();
        //run insert raw for invoice_css and invoice_template
        $config = $seederData->getConfigData();
        $dataConvert = [];
        foreach ($config as $row) {
            $name = $row['name'];
            $slug = $row['slug'];
            $value = $row['value'];
            $dataConvert[] = "('$name', '$slug', '$value')";
        }
        $sql = "INSERT INTO ".$prefix."config (name, slug, value) values ";
        $sql .= implode(',', $dataConvert);
        DB::unprepared($sql);

        //DB::unprepared(File::get(base_path('database/seeds/seeder.sql')));

        //block
        $block = $seederData->getBlockData();
        $block = Functions::removeRnt($block);
        \Modules\Backend\Models\Block::insert($block);

        //page
        $page = $seederData->getPageData();
        \Modules\Backend\Models\Page::insert($page);

        //insert raw for mail template
        $mail = $seederData->getMailTemplateData();
        $dataConvert = [];
        foreach ($mail as $row) {
            $name = $row['name'];
            $content = $row['mail_content'];
            $dataConvert[] = "('$name', '$content')";
        }
        $sql = "INSERT INTO ".$prefix."mail_template (name, mail_content) values ";
        $sql .= implode(',', $dataConvert);
        DB::unprepared($sql);
    }

}