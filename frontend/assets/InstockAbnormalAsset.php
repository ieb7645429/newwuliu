<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class InstockAbnormalAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/instock';

    public $css = [
            'index.css'
    ];
    public $js = [
            'abnormal.js'
    ];
}
