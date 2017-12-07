<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnUpdateTwoAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/return';
    
    public $css = [
        'update2.css'
    ];
    public $js = [
        'update2.js'
    ];
}