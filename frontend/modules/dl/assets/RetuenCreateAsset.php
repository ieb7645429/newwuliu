<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class RetuenCreateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/return';
    
    public $css = [
        'create.css'
    ];
    public $js = [
        'create.js'
    ];
}
