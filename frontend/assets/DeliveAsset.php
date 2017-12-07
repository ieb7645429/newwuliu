<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DeliveAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/logistics-order-delive';

    public $js = [
            'index.js'
    ];
}
