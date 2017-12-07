<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnCompleteOverAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/return-complete';

    public $css = [
            'return-over.css'
    ];
    public $js = [
            'index.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
