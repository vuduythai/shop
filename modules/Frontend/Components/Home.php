<?php

namespace Modules\Frontend\Components;

use Illuminate\Database\Eloquent\Model;
use Modules\Backend\Models\Label;
use Modules\Frontend\Classes\Frontend;

class Home extends Model
{

    /**
     * Get data gallery in homepage
     */
    public static function gallery()
    {
        $data = Frontend::getThemeImagesBySlug('homepage-slider');
        return $data;
    }

    /**
     * Get brand in homepage
     */
    public static function brand()
    {
        $data = Frontend::getThemeImagesBySlug('homepage-brand');
        return $data;
    }


}