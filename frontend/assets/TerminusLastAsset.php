<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class TerminusLastAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/terminus';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'last.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
