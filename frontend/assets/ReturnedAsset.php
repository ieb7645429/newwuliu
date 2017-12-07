<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnedAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/return';

    public $css = [
        'list.css',
    ];
    
    public $js = [
        'returned.js'
    ];
    
    public $depends = [
        'components\Lodop\LodopAsset'
    ];
}
