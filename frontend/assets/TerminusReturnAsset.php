<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class TerminusReturnAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/terminus';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'return.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
