<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class RetuenCreateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/return';
    
    public $css = [
        'create.css'
    ];
    public $js = [
        'create.js'
    ];
}
