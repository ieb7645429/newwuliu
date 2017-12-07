<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SmallPrintAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/small-print';

    public $css = [
            'index.css'
    ];
    public $js = [
        'view.js'
    ];
    
    public $depends = [
        'components\Lodop\DalianAsset'
    ];
}
