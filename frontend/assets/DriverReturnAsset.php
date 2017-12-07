<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverReturnAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/driver';
    
    public $css = [
        'list.css',
    ];
    
    public $js = [
        'list.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
