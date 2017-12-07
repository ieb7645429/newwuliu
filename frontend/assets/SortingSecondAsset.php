<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SortingSecondAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/sorting';

    public $css = [
            'index.css'
    ];
    public $js = [
        'index-second.js'
    ];
}