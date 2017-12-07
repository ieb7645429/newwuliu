<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverManagerCityWideAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/driver-manager';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'city-wide.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
