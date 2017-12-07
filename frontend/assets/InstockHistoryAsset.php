<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class InstockHistoryAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/instock';

    public $js = [
            'index.js'
    ];
    public $css = [
            'history-list.css'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
