<?php namespace Modules;

use Illuminate\Support\Facades\App;
use Twig_Environment;

class Twig
{

    public function parse($contents, $vars = [])
    {
        $loader = App::make('twig.loader');
        $twig = new Twig_Environment($loader, []);
        $twig->addExtension(new TwigFilterExtend());//add custom filter
        $template = $twig->createTemplate($contents);
        return $template->render($vars);
    }
}
