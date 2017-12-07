<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class RetuenUpdateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/return';
    
    public $css = [
        'create.css'
    ];
    
    public $js = [
        'update.js'
    ];
}
