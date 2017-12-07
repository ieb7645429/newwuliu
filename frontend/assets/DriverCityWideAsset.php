<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverCityWideAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/driver';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'cityWide.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
