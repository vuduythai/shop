<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Css Files
	|--------------------------------------------------------------------------
	|
	| Css file of your style for your emails
	| The content of these files will be added directly into the inliner
	| Use absolute paths, ie. public_path('css/main.css')
	|
	*/

    'css-files' => [
        base_path('Themes/'.env('THEME_NAME', 'base').'/assets/css/mail.css')
    ],


];
