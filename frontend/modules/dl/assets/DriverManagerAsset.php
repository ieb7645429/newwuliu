<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverManagerAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/driver-manager';

    public $css = [
        'index.css',
    ];
    public $js = [
        'index.js',
    ];
    
   
}
