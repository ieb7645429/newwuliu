<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverManagerAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/driver-manager';

    public $css = [
        'index.css',
    ];
    public $js = [
        'index.js',
    ];
    
   
}
