<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/driver';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'index.js'
    ];
    
    public $depends = [
        'components\Lodop\LodopAsset'
    ];
}
