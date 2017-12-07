<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverManagerCityWideAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/driver-manager';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'city-wide.js'
    ];
    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}
