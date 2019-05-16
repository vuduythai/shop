<?php
namespace Modules;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Core\System;
use Modules\Backend\Middleware\BackendAcl;
use Modules\Backend\Middleware\BackendAuth;
use Modules\Backend\Core\ModelObserve;
use Illuminate\Support\Facades\Blade;
use Modules\Backend\Middleware\BackendLocale;
use Modules\Frontend\Middleware\FrontendAuth;
use Modules\Backend\Models\Config;
use Modules\Backend\Models\Currency;
use Modules\Frontend\Middleware\FrontendLocale;
use Modules\Install\Middleware\CanInstall;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //to load routes.php and define path hint
        include __DIR__.'/routes.php';//include routes of web
        //namespace psr-4 folder name sensitive => load class
        //load admin
        $this->loadViewsFrom(__DIR__.'/Backend/views', 'Backend.View');
        $this->loadTranslationsFrom(__DIR__. '/Backend/lang', 'Backend.Lang');
        //load install
        $this->loadViewsFrom(__DIR__.'/Install/views', 'Install.View');
        $this->loadTranslationsFrom(__DIR__. '/Install/lang', 'Install.Lang');

        //observe to know if need to clear cache
        $modelFactory = AppModel::factoryModelBackend();
        foreach ($modelFactory as $key => $value) {
            if ($key != 'backend_user') {//ignore backend_user
                $value::observe(ModelObserve::class);
            }
        }

        Blade::directive('imageDisplay', function ($imagePath) {
            $baseUrl = URL::to('/');
            $noImagePath = '"'.URL::to('/modules/backend/assets/img/no_image.jpg').'"';
            $path = '"'.$baseUrl.System::FOLDER_IMAGE.'".'.$imagePath;
            return "<?php
                if ($imagePath == '') {
                    echo $noImagePath;
                } else {
                    echo $path;
                }
            ?>";
        });

        Blade::directive('displayPriceAndCurrency', function ($price) {
            $convert = self::displayPriceAndCurrency($price);
            return "<?php echo $convert ?>";
        });
    }

    /**
     * Display price and currency
     */
    public static function displayPriceAndCurrency($price)
    {
        $currency = Config::getCurrencySymbol();
        if ($currency['symbol_position'] == Currency::POSITION_BEFORE) {//before
            return ' " '. $currency['symbol']. ' ". ' .$price;
        } else {//after
            return $price. ' ." '. $currency['symbol'] . ' " ';
        }
    }

    public function register()
    {
        require_once base_path() . '/modules/IHelpers.php';
        //add middleware for authen and acl
        $this->app['router']
            ->aliasMiddleware('backend.auth', BackendAuth::class);
        $this->app['router']
            ->aliasMiddleware('backend.acl', BackendAcl::class);
        $this->app['router']
            ->aliasMiddleware('frontend.auth', FrontendAuth::class);
        $this->app['router']
            ->aliasMiddleware('canInstall', CanInstall::class);
        $this->app['router']
            ->aliasMiddleware('frontend.locale', FrontendLocale::class);
        $this->app['router']
            ->aliasMiddleware('backend.locale', BackendLocale::class);
        $this->app->singleton('parse.twig', function ($app) {
            return new Twig;
        });
    }
}
