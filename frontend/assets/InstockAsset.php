<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class InstockAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/instock';

    public $css = [
            'index.css'
    ];
    public $js = [
        'index.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
